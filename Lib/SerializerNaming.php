<?php

App::uses('Inflector', 'Utility');

/**
 * SerializerNaming
 */
class SerializerNaming {
	/**
	 * @access protected
	 * @var String $suffix
	 */
	protected $suffix = 'Serializer';

	/**
	 * Convert a supplied string to a conventionally named Serializer class.
	 *
	 * @param String $name
	 * @return String
	 */
	public function classify($name = null) {
		return Inflector::classify($this->stripSuffix($name)) . $this->suffix;
	}

	/**
	 * @param String $name
	 * @return String
	 */
	protected function stripSuffix($name) {
		return preg_replace("/{$this->suffix}$/", '', $name);
	}
}