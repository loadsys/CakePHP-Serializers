<?php

App::uses('SerializerFactory', 'Serializers.Lib');

/**
 * Serialization
 */
class Serialization {
	/**
	 * @access protected
	 * @var String $_name
	 */
	protected $_name = '';

	/**
	 * @access protected
	 * @var Array $_data
	 */
	protected $_data = array();

	/**
	 * Fill me in.
	 *
	 * @access public
	 * @param String $name
	 * @param Array $data
	 */
	public function __construct($name, $data = array()) {
		$this->_name = $name;
		$this->_data = $data;
	}

	/**
	 * With the name and data supplied on construction, convert the data to
	 * jsonapi compliant array that can be encoded as json.
	 *
	 * @access public
	 * @return Array
	 */
	public function parse() {
		$data = $this->normalizeData($this->_data);
		$serializer = $this->factoryFor($this->_name)->generate();
		return $serializer->toJsonApi($data);
	}

	public function deparse() {
		$serializer = $this->factoryFor($this->_name)->generate();
		return $serializer->fromJsonApi($this->_data);
	}

	/**
	 * @access protected
	 * @param Array $data
	 * @return Array
	 */
	protected function normalizeData($data) {
		if (!is_int(key($data))) {
			$data = array($data);
		}
		return $data;
	}

	/**
	 * @access protected
	 * @param String $name
	 * @return Object
	 */
	protected function factoryFor($name) {
		return new SerializerFactory($name);
	}
}
