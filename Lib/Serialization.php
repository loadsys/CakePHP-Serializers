<?php
/**
 * Parses and Deparses JsonAPI complaint data arrays
 *
 * @package  Serializers.Lib
 */
App::uses('SerializerFactory', 'Serializers.Lib');

/**
 * Serialization
 */
class Serialization {

	/**
	 * the name of the root object being serialized/deserialized
	 *
	 * @var String $_name
	 */
	protected $name = '';

	/**
	 * the data being serialzied/deserialized
	 *
	 * @var Array $_data
	 */
	protected $data = array();

	/**
	 * Construct a new instance of Serialization passing the name and the data
	 *
	 * @access public
	 * @param String $name
	 * @param Array $data
	 */
	public function __construct($name, $data = array()) {
		$this->name = $name;
		$this->data = $data;
	}

	/**
	 * With the name and data supplied on construction, convert the data to
	 * jsonapi compliant array that can be encoded as json.
	 *
	 * @return Array
	 */
	public function serialize() {
		$data = $this->normalizeData($this->data);
		$serializer = $this->factoryFor($this->name)->generate();
		return $serializer->serialize($data);
	}

	/**
	 * With the name and data supplied on construction, convert the data from
	 * jsonapi compliant array to a CakePHP standard array
	 *
	 * @return Array
	 */
	public function deserialize() {
		$serializer = $this->factoryFor($this->name)->generate();
		return $serializer->deserialize($this->data);
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
