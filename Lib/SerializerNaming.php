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
	 * @var string
	 */
	protected $suffix = 'Serializer';

	/**
	 * Convert a supplied string to a conventionally named Serializer class.
	 *
	 * @param  string $name the name for the class to generate
	 * @return string       the name converted as needed
	 */
	public function classify($name = null) {
		return Inflector::classify($this->stripSuffix($name)) . $this->suffix;
	}

	/**
	 * Removes a suffix from a provided name
	 *
	 * @param  string $name the name for the class to generate
	 * @return string       the name with a possible suffix stripped
	 */
	protected function stripSuffix($name) {
		return preg_replace("/{$this->suffix}$/", '', $name);
	}
}
