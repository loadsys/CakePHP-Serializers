<?php

App::uses('SerializerFactory', 'CakeSerializer.Lib');

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
		return array();
	}
}
