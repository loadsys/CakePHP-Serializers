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

	/**
	 * converts view data to serialized data
	 *
	 * @param  string $name the model name to serialize
	 * @param  array  $data the data to serialize
	 * @return array
	 */
	protected function toJSON($name, $data) {
		$serialization = new Serialization($name, $data);
		return json_encode($serialization->serialize());
	}

	/**
	 * decides to render the response as json
	 *
	 * @return bool
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
	 * determines if the renderAs property for the controller exists
	 *
	 * @return bool
	 */
	protected function controllerRenderAsPropertyExists() {
		return property_exists($this->controller, 'renderAs');
	}

	/**
	 * determines if the renderAs property for the controller is set to json
	 *
	 * @param string $type the value of renderAs we wish to ensure is matched
	 * @return bool
	 */
	protected function checkControllerRenderAs($type = 'json') {
		return strtolower($this->controller->renderAs) === $type;
	}

	/**
	 * sets the data and name of for the view vars to serialize
	 *
	 * @param array arg any additional arguments to append to data
	 * @return bool
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
