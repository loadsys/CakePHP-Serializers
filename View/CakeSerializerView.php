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
	 * the Controller being operated on
	 *
	 * @var Controller
	 */
	protected $controller;

	/**
	 * Construct a new instance of the View class assigning the Controller as a
	 * property of this object
	 *
	 * @param Controller $controller the Controller instance being operated on
	 */
	public function __construct(Controller $controller = null) {
		parent::__construct($controller);
		$this->controller = $controller;
	}

	/**
	 * Render the view, if it can be rendered as JSON do so, else call the parent
	 * render method
	 *
	 * @param mixed $view the view data
	 * @param string $layout the layout to use
	 * @return string
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
	 * @return string
	 */
	protected function toJSON($name, $data) {
		$serialization = new Serialization($name, $data);
		return json_encode($serialization->serialize());
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
		return property_exists($this->controller, 'renderAs');
	}

	/**
	 * @access protected
	 * @return Boolean
	 */
	protected function checkControllerRenderAs($type = 'json') {
		return strtolower($this->controller->renderAs) === $type;
	}

	/**
	 * @access protected
	 * @return Boolean
	 */
	protected function parseNameAndData($arg = null) {
		$data = array();
		if (isset($this->controller->viewVars['data'])) {
			$data = $this->controller->viewVars['data'];
		}
		if (is_array($arg)) {
			$data = $arg;
		}
		return array($this->controller->name, $data);
	}
}
