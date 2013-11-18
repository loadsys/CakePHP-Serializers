<?php

App::uses('View', 'View');

class CakeSerializerView extends View {
	/**
	 * @access protected
	 * @var Controller $controller
	 */
	protected $controller;

	/**
	 * Fill me in.
	 *
	 * @access public
	 * @param Controller $controller
	 */
	public function __construct(Controller $controller = null) {
		parent::__construct($controller);
		$this->controller = $controller;
		if (
			isset($controller->response) &&
			$controller->response instanceof CakeResponse
		) {
			$controller->response->type('json');
		}
	}

	/**
	 * Fill me in.
	 *
	 * @param  Mixed $view
	 * @param  Null $layout
	 * @return String
	 */
	public function render($view = null, $layout = null) {
		// When $view is null, use controller->name for name and viewVars['data'] for data
		// When $view is string use $view for name and viewVars['data'] for data
		// When $view array, use controller->name for name, but $view for data

		return parent::render($view, $layout);
	}
}