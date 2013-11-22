<?php

App::uses('Object', 'Core');
App::uses('Inflector', 'Utility');

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
	 * List of attributes for this model to be serialized into the
	 * array.
	 *
	 * @access public
	 * @var Array $attributes
	 */
	public $attributes = array();

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
	 * @access protected
	 * @param Array $record
	 * @return Array
	 */
	protected function serializeRecord($record) {
		$index = array_fill_keys($this->attributes, true);
		$data = array_intersect_key($record[$this->rootKey], $index);
		foreach ($this->attributes as $key) {
			if (method_exists($this, $key)) {
				$data[$key] = $this->{$key}($data);
			}
		}
		return $data;
	}
}

