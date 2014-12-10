<?php
/**
 * Serializers and Deserializes data to and from jsonapi format
 *
 * @package  Serializers.Serializer
 */
App::uses('Object', 'Core');
App::uses('Inflector', 'Utility');
App::uses('Serialization', 'Serializers.Lib');

/**
 * Custom exception when the Serializer is missing a required attribute
 */
class SerializerMissingRequiredException extends Exception {
}

/**
 * Custom exception when the Serializer is set to ignore an attribute
 */
class SerializerIgnoreException extends Exception {
}

/**
 * Custom exception when the Deserializer is set to ignore an attribute
 */
class DeserializerIgnoreException extends Exception {
}

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

	public function serializeModel($modelName, $dataForModel) {
		$Serialization = new Serialization($modelName, $dataForModel);
		return $Serialization->serialize($modelName, $dataForModel);
	}

	/**
	 * Convert the supplied normalized data array to jsonapi format.
	 *
	 * @param array $data the data to serialize
	 * @return array
	 */
	public function serialize($unserializedData = array()) {
		if (empty($unserializedData)) {
			return $unserializedData;
		}

		// Might be multiple hasMany records, or a single hasOne or belongsTo record.
		if (
			isset($unserializedData[$this->rootKey])
			&& !empty($unserializedData[$this->rootKey])
			&& !array_key_exists(0, $unserializedData[$this->rootKey])
		) {
			$serializedData = $this->_convertAssociated($this->rootKey, $unserializedData);
		} elseif (
			isset($unserializedData[$this->rootKey])
			&& array_key_exists(0, $unserializedData[$this->rootKey])
		) {
			$serializedData = $this->_convertMany($this->rootKey, $unserializedData[$this->rootKey]);
		} elseif (
			!isset($unserializedData[$this->rootKey])
			&& array_key_exists(0, $unserializedData)
		) {
			$serializedData = $this->_convertMany($this->rootKey, $unserializedData);
		} else {
			$serializedData = array();
		}

		$serializedData = array(
			Inflector::tableize($this->rootKey) => $serializedData,
		);

		return $this->afterSerialize($serializedData, $unserializedData);
	}

	/**
	 * Convert data from a find('all') style query by converting each indexed result.
	 *
	 * @param	string	$modelName	The Model->alias name on which the find was performed.
	 * @param	array	$data		Numerically indexed results from a find('all') query.
	 * @return	array				Transformed data in an array that can conforms to JSON API.
	 */
	protected function _convertMany($modelName, $data) {
		if (empty($data)) {
			return $data;
		}

		$jsonData = array();
		foreach ($data as $index => $record) {
			// Might be multiple hasMany records, or a single hasOne or belongsTo record.
			if (isset($record[$modelName])) {
				$jsonData[$index] = $this->_convertAssociated($modelName, $record);
			} else {
				$jsonData[$index] = $this->_convertSingle($index, $record);
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
	 *
	 * @param	string	$modelName	The Model->alias name on which the find was performed.
	 * @param	array	$data		A record as produced by a find('first') query.
	 * @return	array				Transformed data in an array that can conforms to JSON API.
	 */
	protected function _convertAssociated($primaryModel, $data) {
		// Prime the record with the primary model's data.
		$jsonData = $this->_convertSingle($primaryModel, $data[$primaryModel]);
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
	 *
	 * @param	string	$modelName	The Model->alias name on which the find was performed.
	 * @param	array	$data		A record as produced by a find('first') query.
	 * @return	array				Transformed data in an array that can conforms to JSON API.
	 */
	protected function _convertSingle($modelName, $data) {
		$jsonData = $this->convertFields($data);

		// Process any nested arrays.
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				if (array_key_exists(0, $data[$key])) {
					$jsonData = array_merge($jsonData, $this->serializeModel($key, $value));
				} else {
					$recordsForSubModel[$key] = $value;
					$jsonData = array_merge($jsonData, $this->serializeModel($key, $recordsForSubModel));
					/*$this->_convertSingle($key, $value)*/;
				}
			}
		}
		foreach ($jsonData as $key => $val) {
			if (is_array($val) && count($val) === 1 && array_key_exists(0, $val)) {
				$jsonData[$key] = array_pop($val);
			}
		}

		return $jsonData;
	}

	protected function convertFields($data) {
		$whitelistFields = array_merge((array) $this->required, (array) $this->optional);
		$jsonData = array();
		foreach ($whitelistFields as $key ) {
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
	 * Serializes a CakePHP data array into a jsonapi format array
	 *
	 * @param array $unserializedData the unserialized data, typically set in a
	 * Controller to the View
	 * @return array
	 */
	protected function serializeData($unserializedData) {

		/*
		$serializedData = array();

		foreach ($unserializedData as $key => $record) {

			$className = Inflector::classify($key);
			$tableName = Inflector::tableize($key);

			if($className !== $this->rootKey) {
				$recordsToProcess[$className] = $unserializedData[$key];
				$Serialization = new Serialization($className, $recordsToProcess);
				$subModelSerializedData = $Serialization->serialize($className, $recordsToProcess);

				if (is_array($subModelSerializedData)) {
					$serializedData = $serializedData + $subModelSerializedData;
				} else {
					$serializedData[$tableName] = $subModelSerializedData;
				}
			} else {
				if (!empty($record)) {
					$serializedData[$tableName] = $this->serializeRecord($className, $record);
				} else {
					$serializedData[$tableName] = array();
				}
			}
		}
		return $serializedData;
		*/
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
	 * serialize a record
	 *
	 * @param  string $currentClassName the name of the class being operated on
	 * @param  array $currentRecord    the current record being serialized
	 * @return array
	 */
	protected function serializeRecord($currentClassName, $currentRecord) {
		/*
		$serializedData = array();

		foreach ($currentRecord as $key => $data) {
			if (is_int($key)) {
				$serializedData[] = $this->serializeRecord($currentClassName, $data);
			} else {
				$methodName = "serialize_{$key}";

				if (method_exists($this, $methodName)) {
					$this->validateSerializedRequiredAttributes($currentRecord);
					// if there exists a method for the current key process it
					try {
						$serializedData[$key] = $this->{$methodName}($serializedData, $currentRecord);
					} catch (SerializerIgnoreException $e) {
						// if we throw this exception catch it and don't set any data for that record
					}
				} elseif (is_array($data)) {
					$classifiedSubModelKey = Inflector::classify($key);
					$tabelizedSubModelKey = Inflector::tableize($key);
					$recordsToProcess[$classifiedSubModelKey] = $currentRecord[$key];
					$Serialization = new Serialization($classifiedSubModelKey, $recordsToProcess);
					$subModelSerializedData = $Serialization->serialize($classifiedSubModelKey, $recordsToProcess);

					if (is_array($subModelSerializedData)) {
						$serializedData = $serializedData + $subModelSerializedData;
					} else {
						$serializedData[$tabelizedSubModelKey] = $subModelSerializedData;
					}
				} else {
					if (
						in_array($key, $this->required)
						|| in_array($key, $this->optional)
					) {
						$this->validateSerializedRequiredAttributes($currentRecord);
						$serializedData[$key] = $data;
					}
				}
			}
		}

		return $serializedData;
		*/
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

		foreach ($serializedData as $key => $record) {
			// if the key for this record is an int, multiple records
			$className = Inflector::classify($key);
			$deserializedData[$className] = $this->deserializeRecord($className, $record);
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
					$recordsToProcess[$classifiedSubModelKey] = $currentRecord[$key];
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
