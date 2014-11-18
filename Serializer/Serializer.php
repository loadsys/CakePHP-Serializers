<?php

App::uses('Object', 'Core');
App::uses('Inflector', 'Utility');

class SerializerMissingRequiredException extends Exception {}
class SerializerIgnoreException extends Exception {}

class DeserializerMissingRequiredException extends Exception {}
class DeserializerIgnoreException extends Exception {}

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
	public function toJsonApi(array $data = array()) {
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

	public function fromJsonApi($serializedData = array()) {
		if (empty($serializedData)) {
			return $serializedData;
		}

		$rootKeysInSerializedData = get_object_vars($serializedData);
		$key = Inflector::tableize($this->rootKey);

		if (!array_key_exists($key, (array)$rootKeysInSerializedData)) {
			$msg = "The controller name: $key was not included in the passed in JSON body of the request.";
			throw new DeserializerMissingRequiredException($msg);
		}

		$dataAsArray[$this->rootKey] = $this->deserializeRecord($serializedData->{$key});

		return $dataAsArray;
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
	 * @access protected
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
		$originalOptionals  = $this->optional;
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
	 * deserialize a json api record passed
	 *
	 * @param  object $record [description]
	 * @return array
	 */
	protected function deserializeRecord(stdClass $record) {
		$record = (array)$record;
		$data = $record;
		foreach($record as $key) {
			$methodName = "deserialize_{$key}";
			if (method_exists($this, $methodName)) {
				try {
					$data[$key] = $this->{$methodName}($data, $record);
				} catch (DeserializerIgnoreException $e) {
					unset($data[$key]);
				}
			}
		}
		return $this->afterDeserialize($data, $record);
	}
}

