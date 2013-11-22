<?php

App::uses('SerializerNaming', 'CakeSerializers.Lib');

/**
 * SerializerFactory
 */
class SerializerFactory {
	/**
	 * @access protected
	 * @var String $_className
	 */
	protected $_className = null;

	/**
	 * New SerializerFactories take the name of the class that they can create
	 * in the constructor, but can create many instances of that class using the
	 * generate method.
	 *
	 * @access public
	 * @param String $className
	 */
	public function __construct($className = null) {
		$naming = new SerializerNaming;
		$this->_className = $naming->classify($className);
	}

	/**
	 * Creates a new instance if the serializer with the supplied data.
	 *
	 * @access public
	 * @param Array $data
	 * @return Serializer
	 */
	public function generate() {
		App::uses($this->_className, 'Serializer');
		if (!class_exists($this->_className)) {
			$this->generateError();
		}
		return new $this->_className();
	}

	/**
	 * @access protected
	 * @throws LogicException
	 */
	protected function generateError() {
		$c = $this->_className;
		$msg  = "Could not find class %s. Create `class %s extends Serializer` ";
		$msg .= "in APP/Serializer/%s.php.";
		throw new LogicException(sprintf($msg, $c, $c, $c));
	}
}
