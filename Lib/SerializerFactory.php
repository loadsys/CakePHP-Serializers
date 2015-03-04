<?php
/**
 * Generates a new instance of the Serializer Class
 *
 * @package  Serializers.Lib
 */
App::uses('SerializerNaming', 'Serializers.Lib');
App::uses('Serializer', 'Serializers.Serializer');
App::uses('ClassRegistry', 'Utility');

/**
 * SerializerFactory
 */
class SerializerFactory {

	/**
	 * the class name to generate a Serializer instance
	 *
	 * @var String $className
	 */
	protected $className = null;

	/**
	 * New SerializerFactories take the name of the class that they can create
	 * in the constructor, but can create many instances of that class using the
	 * generate method.
	 *
	 * @param string $className the name of the class to generate a Serializer
	 */
	public function __construct($className = null) {
		$naming = new SerializerNaming;
		$this->className = $naming->classify($className);
	}

	/**
	 * Creates a new instance if the serializer with the supplied data.
	 *
	 * @return Serializer
	 */
	public function generate() {
		App::uses($this->className, 'Serializer');
		$modelName = preg_replace('/Serializer$/', '', $this->className);

		if (!class_exists($this->className)) {

			// try to load the model name, catch if an exception occurs
			try {
				$model = ClassRegistry::init($modelName);
				$required = array_keys($model->schema());
			} catch (MissingTableException $e) {
				$required = array();
			}

			$serializer = new Serializer();
			$serializer->rootKey = $modelName;
			$serializer->required = $required;
			return $serializer;
		}

		$serializer = new Serializer();
		$serializer->rootKey = $modelName;
		return $serializer;
	}
}
