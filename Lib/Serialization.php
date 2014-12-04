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
	 * @param string $name the class name of object being serialized
	 * @param array $data the data to serialize
	 * @return void
	 */
	public function __construct($name, $data = array()) {
		$this->name = $name;
		$this->data = $data;
	}

	/**
	 * With the name and data supplied on construction, convert the data to
	 * jsonapi compliant array that can be encoded as json.
	 *
	 * @return array
	 */
	public function serialize() {
		$data = $this->normalizeDataForSerialization($this->data);
		$serializer = $this->factoryFor($this->name)->generate();
		return $serializer->serialize($data);
	}

	/**
	 * With the name and data supplied on construction, convert the data from
	 * jsonapi compliant array to a CakePHP standard array
	 *
	 * @return array
	 */
	public function deserialize() {
		$serializer = $this->factoryFor($this->name)->generate();
		return $serializer->deserialize($this->data);
	}

	/**
	 * normalize the data when serializing the data
	 *
	 * @param array $data the data to serialize
	 * @return array
	 */
	protected function normalizeDataForSerialization($data) {
		if (!is_int(key($data))) {
			$data = array($data);
		}
		return $data;
	}

	/**
	 * return a new instance of the Serializer, using the SerializerFactory to
	 * generate the instance
	 *
	 * @param string $name the name of the class to generate
	 * @return Object
	 */
	protected function factoryFor($name) {
		return new SerializerFactory($name);
	}
}
