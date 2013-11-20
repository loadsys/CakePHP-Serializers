<?php

App::uses('View', 'View');
App::uses('CheckRequest', 'CakeSerializers.Lib');

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
	 * @param  Mixed $view
	 * @param  Null $layout
	 * @return String
	 */
	public function render($view = null, $layout = null) {
		$render = '';
		$checkRequest = new CheckRequest($this->_controller);
		if ($checkRequest->isJSON()) {
			$response->type('json');
			// When $view is null, use controller->name for name and viewVars['data'] for data
			// When $view is string use $view for name and viewVars['data'] for data
			// When $view array, use controller->name for name, but $view for data
			// $render = Something
		} else {
			$render = parent::render($view, $layout);
		}
		return $render;
	}
}
