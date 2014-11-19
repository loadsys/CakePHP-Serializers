<?php
/**
 * Custom view class for rending serialized data
 *
 * @package  Serializers.View
 */
App::uses('View', 'View');
App::uses('AnalyzeRequest', 'Serializers.Lib');
App::uses('Serialization', 'Serializers.Lib');

/**
 * CakeSerializerView class
 */
class CakeSerializerView extends View {

	/**
	 * @access protected
	 * @var Controller $_controller
	 */
	protected $_controller;

	/**
	 * Fill me in.
	 *
	 * @access public
	 * @param Controller $controller
	 */
	public function __construct(Controller $controller = null) {
		parent::__construct($controller);
		$this->_controller = $controller;
	}

	/**
	 * Fill me in.
	 *
	 * @access public
	 * @param Mixed $view
	 * @param Null $layout
	 * @return String
	 */
	public function render($view = null, $layout = null) {
		if ($this->renderAsJSON()) {
			$this->response->type('json');
			list($name, $data) = $this->parseNameAndData($view);
			$render = $this->toJSON($name, $data);
		} else {
			$render = parent::render($view, $layout);
		}
		return $render;
	}

	/**
	 * @access protected
	 * @return String
	 */
	protected function toJSON($name, $data) {
		$serialization = new Serialization($name, $data);
		return json_encode($serialization->parse());
	}

	/**
	 * @access protected
	 * @return Boolean
	 */
	protected function renderAsJSON() {
		if ($this->controllerRenderAsPropertyExists()) {
			return $this->checkControllerRenderAs();
		} else {
			$analyzer = new AnalyzeRequest($this->request);
			return $analyzer->isJSON();
		}
	}

	/**
	 * @access protected
	 * @return Boolean
	 */
	protected function controllerRenderAsPropertyExists($type = 'json') {
		return property_exists($this->_controller, 'renderAs');
	}

	/**
	 * @access protected
	 * @return Boolean
	 */
	protected function checkControllerRenderAs($type = 'json') {
		return strtolower($this->_controller->renderAs) === $type;
	}

	/**
	 * @access protected
	 * @return Boolean
	 */
	protected function parseNameAndData($arg = null) {
		$data = array();
		if (isset($this->_controller->viewVars['data'])) {
			$data = $this->_controller->viewVars['data'];
		}
		if (is_array($arg)) {
			$data = $arg;
		}
		return array($this->_controller->name, $data);
	}
}
