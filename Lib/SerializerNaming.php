<?php
/**
 * Name a Serializer class
 *
 * @package  Serializers.Lib
 */
App::uses('Inflector', 'Utility');

/**
 * SerializerNaming
 */
class SerializerNaming {

	/**
	 * the suffix used for naming a Serializer class
	 *
	 * @var String $suffix
	 */
	protected $_suffix = 'Serializer';

	/**
	 * Convert a supplied string to a conventionally named Serializer class.
	 *
	 * @param String $name
	 * @return String
	 */
	public function classify($name = null) {
		return Inflector::classify($this->stripSuffix($name)) . $this->_suffix;
	}

	/**
	 * @param String $name
	 * @return String
	 */
	protected function stripSuffix($name) {
		return preg_replace("/{$this->_suffix}$/", '', $name);
	}
}
