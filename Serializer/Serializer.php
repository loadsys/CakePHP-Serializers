<?php

App::uses('Object', 'Core');
App::uses('Inflector', 'Utility');

class Serializer extends Object {
	public $attributes = array();
	public $pretty = false;

	protected $_inflectRoot = true;

	public function __construct(Controller $controller, array $options = array()) {
		$this->parseOptions($options + $this->parseController($controller));
	}

	public function toJson(array $data = array()) {
		if (empty($data)) {
			return $this->encode($data);
		}

		$compiled = array();
		$isSingular = $this->isSingular($data);
		if ($isSingular) {
			$compiled = $this->serializeRecord($data);
		} else {
			foreach ($data as $record) {
				$compiled[] = $this->serializeRecord($record);
			}
		}

		if (!$this->root) {
			$str = $this->encode($compiled);
		} else {
			$str = $this->encode(array($this->inflectedRoot($isSingular) => $compiled));
		}
		return $str;
	}

	public function encode($data = array()) {
		if ($this->pretty && defined('JSON_PRETTY_PRINT')) {
			return json_encode($data, JSON_PRETTY_PRINT);
		}
		return json_encode($data);
	}

	public function serializeRecord($record) {
		$dataKey = Inflector::classify(Inflector::singularize($this->name));
		return $record[$dataKey];
	}

	protected function inflectedRoot($isSingular) {
		if (!$this->_inflectRoot) {
			return $this->root;
		}
		if ($isSingular) {
			return Inflector::singularize($this->root);
		} else {
			return Inflector::pluralize($this->root);
		}
	}

	protected function parseController($controller) {
		return array(
			'name' => $controller->name
		);
	}

	protected function parseOptions($options = array()) {
		if (!isset($this->name)) {
			$this->name = $options['name'];
		}

		if (!isset($this->root)) {
			if (isset($options['root'])) {
				$this->_inflectRoot = false;
				$this->root = $options['root'];
			} else {
				$this->root = Inflector::underscore($options['name']);
			}
		} else {
			$this->_inflectRoot = false;
		}

		if (isset($options['pretty'])) {
			$this->pretty = (boolean) $options['pretty'];
		}
	}

	protected function isSingular(array $data) {
		return !is_numeric(key($data));
	}
}

