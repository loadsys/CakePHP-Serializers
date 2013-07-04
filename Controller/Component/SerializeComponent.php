<?php

App::uses('Component', 'Controller/Component');

class SerializeComponent extends Component {
	protected $serializerName = false;

	public function startup(Controller $controller) {
		$this->controller = $controller;
	}

	public function toJson(array $data = array(), array $options = array()) {
		$className = null;
		if (isset($options['serializer']) && is_string($options['serializer'])) {
			$className = $options['serializer'];
			unset($options['serializer']);
		}
		$className = $this->serializerName($className);
		if (!class_exists($className)) {
			App::uses($className, 'Serializer');
		}
		if (!class_exists($className)) {
			throw new Exception("{$className} could not be found. Create {$className}.php in APP/Serializer");
		}

		$serializer = new $className($this->controller, $options);
		return $serializer->toJson($data);
	}

	public function serializerName($name = null) {
		if (!$name) {
			$name = $this->serializerName;
		}
		if (!$name) {
			$name = $this->controller->name;
		}
		return str_replace('Serializer', '', $name) . 'Serializer';
	}

	public function with($className = '') {
		if (!empty($className)) {
			$this->serializerName = $className;
		}
	}
}

