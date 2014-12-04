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

	/**
	 * Convert the supplied normalized data array to jsonapi format.
	 *
	 * @access public
	 * @param Array $data
	 * @return Array
	 */
	public function serialize($data = array()) {
		if (empty($data)) {
			return $data;
		}
		$rows = array();
		foreach ($data as $v) {
			// ensure there is some data being passed to serialize record
			if (!empty($v)) {
				$rows[] = $this->serializeRecord($v);
			}
		}
		$key = Inflector::tableize($this->rootKey);
		return array($key => $rows);
	}

	/**
	 * from jsonapi format to CakePHP array
	 *
	 * @param  array  $serializedData the serialized data in jsonapi format
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
	 * Callback method called after automatic serialization. Whatever is returned
	 * from this method will ultimately be used as the JSON response.
	 *
	 * @param  multi  $json serialized record data
	 * @param  multi  $data raw record data
	 * @return multi
	 */
	public function afterSerialize($json, $record) {
		return $json;
	}

	/**
	 * Callback method called after automatic deserialization. Whatever is returned
	 * from this method will ultimately be used as the Controller->data for cake
	 *
	 * @param  multi  $data deserialized record data
	 * @param  multi  $json json record data
	 * @return multi
	 */
	public function afterDeserialize($data, $json) {
		return $data;
	}

	/**
	 * Serializes a CakePHP data array into a jsonapi format array
	 *
	 * @throws SerializerMissingRequiredException If a required attribute is missing
	 * @param Array $record
	 * @return Array
	 */
	protected function serializeRecord($record) {
		// if there is no data return nothing
		if (empty($record)) {
			return;
		}
		$required = $this->required;
		$keys = array_keys($record[$this->rootKey]);
		$requiredCheck = array_diff($required, $keys);
		if (!empty($requiredCheck)) {
			$missing = join(', ', $requiredCheck);
			$msg = "The following keys were missing from $this->rootKey: $missing";
			throw new SerializerMissingRequiredException($msg);
		}
		$originalOptionals = $this->optional;
		if (!is_array($originalOptionals)) {
			$originalOptionals = array();
		}
		$optinals = array_intersect($originalOptionals, $keys);
		$attrs = array_unique(array_merge($required, $optinals));
		$index = array_fill_keys($attrs, true);
		$initialData = array();
		foreach ($record[$this->rootKey] as $key => $value) {
			if (!ctype_upper($key[0]) && in_array($key, $attrs) ) {
				$initialData[$key] = $value;
			}
		}
		$others = array_diff($originalOptionals, $attrs);
		foreach ($others as $key) {
			$methodName = "serialize_{$key}";
			if (method_exists($this, $methodName)) {
				array_push($attrs, $key);
			}
		}
		$data = array_intersect_key($initialData, $index);
		foreach ($attrs as $key) {
			$methodName = "serialize_{$key}";
			if (method_exists($this, $methodName)) {
				try {
					$data[$key] = $this->{$methodName}($data, $record);
				} catch (SerializerIgnoreException $e) {
					unset($data[$key]);
				}
			}
		}
		return $this->afterSerialize($data, $record);
	}

	/**
	 * deserialize an array of data
	 *
	 * @param  array  $serializedData stream of data from the request
	 * @return void
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
	 * @param  string $currentClassName the current class name being operated on
	 * @param  array $currentRecord     the current record being operated on
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

