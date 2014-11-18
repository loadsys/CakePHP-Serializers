<?php

App::uses('SerializerNaming', 'Serializers.Lib');
App::uses('Serializer', 'Serializers.Serializer');
App::uses('ClassRegistry', 'Utility');

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
			$modelName = preg_replace('/Serializer$/', '', $this->_className);
			$model = ClassRegistry::init($modelName);
			$serializer = new Serializer();
			$serializer->rootKey = $modelName;
			$serializer->required = array_keys($model->schema());
			return $serializer;
		}
		return new $this->_className();
	}
}
