<?php
/**
 * Base custom view class for rending serialized data
 *
 * @package  Serializers.View
 */
App::uses('View', 'View');
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
	 * Render the view, determining the Request type and changing both the response
	 * headers and the format dependent upon the Accept header
	 *
	 * @param mixed $view the view data
	 * @param string $layout the layout to use
	 * @return string
	 */
	public function render($view = null, $layout = null) {
		if ($this->isJsonApiRequest()) {
			// Set the controller to respond as JSON API
			$this->controller->response->type(array('jsonapi' => 'application/vnd.api+json'));
			$this->controller->response->type('jsonapi');

			list($name, $data) = $this->parseNameAndData($view);
			return $this->toJSON($name, $data);
		} elseif ($this->isJsonRequest()) {
			// Set the controller to respond as JSON
			$this->controller->response->type('json');

			list($name, $data) = $this->parseNameAndData($view);
			return $this->toJSON($name, $data);
		}

		return parent::render($view, $layout);
	}

	/**
	 * converts view data to serialized data
	 *
	 * @param string $name the model name to serialize
	 * @param array  $data the data to serialize
	 * @return array
	 */
	protected function toJSON($name, $data) {
		$serialization = new Serialization($name, $data);
		return json_encode($serialization->serialize());
	}

	/**
	 * sets the data and name of for the view vars to serialize
	 *
	 * @param array $arg any additional arguments to append to data
	 * @return bool
	 */
	protected function parseNameAndData($arg = null) {
		$data = array();

		// if the CakePHP standard variable names exist use them else, fallback
		// to a standard $data variable
		$variableNamePlural = Inflector::variable($this->controller->name);
		$variableNameSingular = Inflector::singularize($variableNamePlural);

		if (
			isset($this->controller->viewVars[$variableNamePlural])
		) {
			$data = $this->controller->viewVars[$variableNamePlural];
		} elseif (
			isset($this->controller->viewVars[$variableNameSingular])
		) {
			$data = $this->controller->viewVars[$variableNameSingular];
		} elseif (
			isset($this->controller->viewVars['data'])
		) {
			$data = $this->controller->viewVars['data'];
		}

		if (is_array($arg)) {
			$data = $arg;
		}
		return array($this->controller->name, $data);
	}

	/**
	 * is this request a JsonApi style request
	 *
	 * @return bool returns true if JsonApi media request, false otherwise
	 */
	protected function isJsonApiRequest() {
		return $this->controller->request->accepts('application/vnd.api+json');
	}
	/**
	 * is this request for Json
	 *
	 * @return bool returns true if Json media request, false otherwise
	 */
	protected function isJsonRequest() {
		return $this->controller->request->accepts('application/json');
	}

}
