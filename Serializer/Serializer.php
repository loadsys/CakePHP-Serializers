<?php
/**
 * Serializers and Deserializes data to and from jsonapi format
 *
 * @package  Serializers.Serializer
 */
App::uses('Object', 'Core');
App::uses('Inflector', 'Utility');
App::uses('Serialization', 'Serializers.Lib');
App::import('Lib/Error', 'Serializers.SerializerExceptions');

/**
 * Serializer
 */
class Serializer extends Object {

	/**
	 * The key name used to find data on the supplied data array
	 *
	 * @access public
	 * @var String $rootKey
	 */
	public $rootKey = false;

	/**
	 * List of required required for this model to be serialized into the
	 * array.
	 *
	 * @access public
	 * @var Array $required
	 */
	public $required = array();

	/**
	 * List of optional required for this model to be serialized into the
	 * array.
	 *
	 * @access public
	 * @var Array $required
	 */
	public $optional = array();

	/**
	 * Generate the rootKey if it wasn't assigned in the class definition
	 *
	 * @access public
	 */
	public function __construct() {
		if (!$this->rootKey) {
			$this->rootKey = preg_replace('/Serializer$/', '', get_class($this));
		}
	}

	/**
	 * helper method to call a sub-serializer class and return the results, in the
	 * instance that a method exists for the name of the model, this calls that
	 * method instead, in this way a serialize_{name} will always override
	 *
	 * @param  string $modelName   the name of the model to serialize
	 * @param  array $dataForModel the array of data for the model to serialize
	 * @return array
	 */
	public function serializeModel($modelName, $dataForModel) {
		$methodName = "serialize_{$modelName}";

		if (method_exists($this, $methodName)) {
			try {
				return $this->{$methodName}(array(), $dataForModel);
			} catch (SerializerIgnoreException $e) {
				// if we throw this exception catch it and don't set any data for that record
			}
		} else {
			$Serialization = new Serialization($modelName, $dataForModel);
			return $Serialization->serialize($modelName, $dataForModel);
		}
	}

	/**
	 * Convert the supplied normalized data array to jsonapi format.
	 *
	 * @param array $unserializedData the data to serialize
	 * @return array
	 */
	public function serialize($unserializedData = array()) {
		if (empty($unserializedData)) {
			$jsonKey = Inflector::tableize($this->rootKey);
			$serializedData = array(
				$jsonKey => array(),
			);
			return $this->afterSerialize($serializedData, $unserializedData);
		}

		// Might be multiple hasMany records, or a single hasOne or belongsTo record.
		if (
			isset($unserializedData[$this->rootKey])
			&& !empty($unserializedData[$this->rootKey])
			&& !array_key_exists(0, $unserializedData[$this->rootKey])
		) {
			// this is an associated record of the rootKey, ie we will have to call
			// sub serializers
			$serializedData = $this->convertAssociated($this->rootKey, $unserializedData);
		} elseif (
			isset($unserializedData[$this->rootKey])
			&& array_key_exists(0, $unserializedData[$this->rootKey])
		) {
			// this is many records for the rootKey Model without the data having the
			// top level Model key
			$serializedData = $this->convertMany($this->rootKey, $unserializedData[$this->rootKey]);
		} elseif (
			!isset($unserializedData[$this->rootKey])
			&& array_key_exists(0, $unserializedData)
		) {
			// this is many records for the rootKey Model with the data having the top
			// level Model key
			$serializedData = $this->convertMany($this->rootKey, $unserializedData);
		} else {
			$serializedData = array();
		}

		// assign the serialized data to the tableized model name array
		$jsonKey = Inflector::tableize($this->rootKey);
		if (
			!array_key_exists(0, $serializedData)
		) {
			$jsonKey = Inflector::singularize($jsonKey);
		}

		$serializedData = array(
			$jsonKey => $serializedData,
		);

		return $this->afterSerialize($serializedData, $unserializedData);
	}

	/**
	 * Convert data from a find('all') style query by converting each indexed result.
	 *
	 * @param  string $modelName The Model->alias name on which the find was performed.
	 * @param  array $data       Numerically indexed results from a find('all') query.
	 * @return array             Transformed data in an array that can conforms to JSON API.
	 */
	protected function convertMany($modelName, $data) {
		$jsonData = array();
		foreach ($data as $index => $record) {
			// Might be multiple hasMany records, or a single hasOne or belongsTo record.
			if (isset($record[$modelName])) {
				$jsonData[$index] = $this->convertAssociated($modelName, $record);
			} else {
				$jsonData[$index] = $this->convertSingle($index, $record);
			}
		}

		if (count($jsonData) === 1) {
			$jsonData = array_pop($jsonData);
		}

		return $jsonData;
	}

	/**
	 * Convert data from a find('first') style query. Transforms adjacent
	 * [RelatedModel] keys into properties containing indexed arrays of
	 * sub-records.
	 *
	 * Sample input:
	 *
	 * 	array(
	 * 		'ResinLot' => array(
	 * 			'id' => '9f90b5d8-563f-11e4-a97c-08002786663d',
	 * 			'resin_product_id' => '79f3f7f0-563f-11e4-a97c-08002786663d',
	 * 			'name' => 'Lot 1',
	 * 			'date_received' => '2014-10-17',
	 * 			'ResinTest' => array('name' => 'whatever'),
	 * 		),
	 * 		'ResinProduct' => array(
	 * 			'id' => '79f3f7f0-563f-11e4-a97c-08002786663d',
	 * 			'name' => 'Product 1',
	 * 		),
	 * 	);
	 *
	 * Sample output:
	 *
	 * 	array(
	 * 		'id' => '9f90b5d8-563f-11e4-a97c-08002786663d',
	 * 		'name' => 'Lot 1',
	 * 		'resin_products' => array(
	 * 			0 => array(
	 * 				'id' => '79f3f7f0-563f-11e4-a97c-08002786663d',
	 * 				'name' => 'Product 1',
	 * 			),
	 * 		),
	 * 	);
	 *
	 * @param	string $primaryModel The primary model name for the top level model
	 * @param	array $data		       the associated data being serialized
	 * @return array				       Transformed data in an array that can conforms to JSON API.
	 */
	protected function convertAssociated($primaryModel, $data) {
		// Prime the record with the primary model's data.
		$jsonData = $this->convertSingle($primaryModel, $data[$primaryModel]);
		unset($data[$primaryModel]);

		// For all other top-level associations, add them as sub-keys to the primary.
		foreach ($data as $modelName => $records) {
			$jsonModelName = Inflector::tableize($modelName);

			if (array_key_exists(0, $data[$modelName])) {
				$jsonData = array_merge($jsonData, $this->serializeModel($modelName, $data));
			} else {
				$recordsForSubModel = array($modelName => $records);
				$jsonData = array_merge($jsonData, $this->serializeModel($modelName, $recordsForSubModel));
			}
		}

		return $jsonData;
	}

	/**
	 * Convert data from a single Cake model record.
	 *
	 * Sample input:
	 *
	 * 	array( // ResinLot
	 * 		'id' => '9f90b5d8-563f-11e4-a97c-08002786663d',
	 * 		'resin_product_id' => '79f3f7f0-563f-11e4-a97c-08002786663d',
	 * 		'name' => 'Lot 1',
	 * 		'date_received' => '2014-10-17',
	 * 		'ResinLotCertificate' => array(
	 * 			0 => array(
	 * 				'id' => 'abcde',
	 * 				'value' => '42',
	 * 		 	),
	 * 		 ),
	 * 	);
	 *
	 * Sample output:
	 *
	 * 	array(
	 * 		'id' => '9f90b5d8-563f-11e4-a97c-08002786663d',
	 * 		'name' => 'Lot 1',
	 * 		'resin_lot_certificates' => array(
	 * 			0 => array(
	 * 				'id' => 'abcde',
	 * 				'value' => '42',
	 * 			),
	 * 	);
	 *
	 * @param	string	$modelName	The Model->alias name on which the find was performed.
	 * @param	array	$data		A record as produced by a find('first') query.
	 * @return	array				Transformed data in an array that can conforms to JSON API.
	 */
	protected function convertSingle($modelName, $data) {
		$jsonData = $this->convertFields($data);

		// Process any nested arrays.
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				if (array_key_exists(0, $data[$key])) {
					$jsonData = array_merge($jsonData, $this->serializeModel($key, $value));
				} else {
					$recordsForSubModel = array($key => $value);
					$jsonData = array_merge($jsonData, $this->serializeModel($key, $recordsForSubModel));
				}
			}
		}

		return $jsonData;
	}

	/**
	 * convert fields for a single model record, both calls methods for a property
	 * as well as validates that required attributes are present
	 *
	 * @param  array $data a single record row for a CakePHP Model array
	 * @return array
	 */
	protected function convertFields($data) {
		$whitelistFields = array_merge(
			(array)$this->required,
			(array)$this->optional
		);
		$jsonData = array();
		foreach ($whitelistFields as $key) {
			if (isset($data[$key])) {
				$methodName = "serialize_{$key}";

				if (method_exists($this, $methodName)) {
					try {
						$jsonData[$key] = $this->{$methodName}($data, $data[$key]);
					} catch (SerializerIgnoreException $e) {
						// if we throw this exception catch it and don't set any data for that record
					}
				} else {
					$jsonData[$key] = $data[$key];
				}
			}
		}
		$this->validateSerializedRequiredAttributes($jsonData);
		return $jsonData;
	}

	/**
	 * Callback method called after automatic serialization. Whatever is returned
	 * from this method will ultimately be used as the JSON response.
	 *
	 * @param multi $serializedData serialized record data
	 * @param multi $unserializedData raw record data
	 * @return multi
	 */
	public function afterSerialize($serializedData, $unserializedData) {
		return $serializedData;
	}

	/**
	 * validate that required attributes for a record are present
	 *
	 * @param  array $record the data for a record
	 * @throws SerializerMissingRequiredException If a required attribute is
	 * missing from record
	 * @return void
	 */
	private function validateSerializedRequiredAttributes($record) {
		$keysInRecord = array_keys($record);
		$requiredCheck = array_diff($this->required, $keysInRecord);
		if (!empty($requiredCheck)) {
			$missing = join(', ', $requiredCheck);
			$msg = "The following keys were missing from $this->rootKey: $missing";
			throw new SerializerMissingRequiredException($msg);
		}
	}

	/**
	 * from jsonapi format to CakePHP array
	 *
	 * @param array $serializedData the serialized data in jsonapi format
	 * @return array
	 */
	public function deserialize($serializedData = array()) {
		if (empty($serializedData)) {
			return $serializedData;
		}
		$deserializedData = array();

		$deserializedData = $this->deserializeData($serializedData);
		return $this->afterDeserialize($deserializedData, $serializedData);
	}

	/**
	 * Callback method called after automatic deserialization. Whatever is returned
	 * from this method will ultimately be used as the Controller->data for cake
	 *
	 * @param multi $deserializedData the deserialized data
	 * @param multi $serializedData   the original un-deserialized data
	 * @return multi
	 */
	public function afterDeserialize($deserializedData, $serializedData) {
		return $deserializedData;
	}

	/**
	 * deserialize an array of serialized data
	 *
	 * @param array $serializedData array of data from the request
	 * @return array
	 */
	protected function deserializeData(array $serializedData = array()) {
		$deserializedData = array();

		$className = Inflector::classify($this->rootKey);
		$tableizedName = Inflector::tableize($this->rootKey);
		$singularizedTableName = Inflector::singularize($tableizedName);

		if (array_key_exists($tableizedName, $serializedData)) {
			$deserializedData[$className] = $this->deserializeRecord($className, $serializedData[$tableizedName]);
		} elseif (array_key_exists($singularizedTableName, $serializedData)) {
			$deserializedData[$className] = $this->deserializeRecord($className, $serializedData[$singularizedTableName]);
		}

		return $deserializedData;
	}

	/**
	 * deserialize a record
	 *
	 * @param string $currentClassName the current class name being operated on
	 * @param array $currentRecord     the current record being operated on
	 * @return array
	 */
	protected function deserializeRecord($currentClassName, $currentRecord) {
		$deserializedData = array();

		foreach ($currentRecord as $key => $data) {
			if (is_int($key)) {
				$deserializedData[] = $this->deserializeRecord($currentClassName, $data);
			} else {
				$methodName = "deserialize_{$key}";

				if (method_exists($this, $methodName)) {
					// if there exists a method for the current key process it
					try {
						$deserializedData[$key] = $this->{$methodName}($deserializedData, $currentRecord);
					} catch (DeserializerIgnoreException $e) {
						// if we throw this exception catch it and don't set any data for that record
					}
				} elseif (is_array($data)) {
					$classifiedSubModelKey = Inflector::classify($key);
					$tableizedName = Inflector::tableize($key);
					$recordsToProcess[$tableizedName] = $currentRecord[$key];
					$Serialization = new Serialization($classifiedSubModelKey, $recordsToProcess);
					$subModelDeserializedData = $Serialization->deserialize($classifiedSubModelKey, $recordsToProcess);
					$deserializedData = $deserializedData + $subModelDeserializedData;
				} else {
					$deserializedData[$key] = $data;
				}
			}
		}

		return $deserializedData;
	}

}
