<?php

App::uses('Object', 'Core');
App::uses('Inflector', 'Utility');

class SerializerMissingRequiredException extends Exception {}
class SerializerIgnoreException extends Exception {}

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
	public function toArray(array $data = array()) {
		if (empty($data)) {
			return $data;
		}
		$rows = array();
		foreach ($data as $v) {
			$rows[] = $this->serializeRecord($v);
		}
		$key = Inflector::tableize($this->rootKey);
		return array($key => $rows);
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
	 * @access protected
	 * @param Array $record
	 * @return Array
	 */
	protected function serializeRecord($record) {
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
			if (method_exists($this, $key)) {
				array_push($attrs, $key);
			}
		}
		$data = array_intersect_key($initialData, $index);
		foreach ($attrs as $key) {
			if (method_exists($this, $key)) {
				try {
					$data[$key] = $this->{$key}($data, $record);
				} catch (SerializerIgnoreException $e) {
					unset($data[$key]);
				}
			}
		}
		return $this->afterSerialize($data, $record);
	}
}

