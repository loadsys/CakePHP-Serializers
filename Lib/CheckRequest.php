<?php
/**
 * CheckRequest
 */
class CheckRequest {
	/**
	 * @access protected
	 * @var Array $_controller
	 */
	protected $_controller;

	/**
	 * @access protected
	 * @var Array $_jsonTypes
	 */
	protected $_jsonTypes = array('application/json');

	/**
	 * Fill me in.
	 *
	 * @access public
	 * @param Controller $controller
	 */
	public function __construct($controller) {
		$this->_controller = $controller;
	}

	/**
	 * Checks various aspects of the controller supplied on initialization
	 * to determine if the request is JSON.
	 *
	 * @access public
	 * @return Boolean
	 */
	public function isJSON() {
		if ($this->controllerRenderAsPropertyExists()) {
			return $this->checkControllerRenderAs();
		} else {
			return (
				$this->checkRequestContentType() ||
				$this->checkRequestAcceptsHeader() ||
				$this->checkRequestExtension()
			);
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
	protected function checkRequestContentType() {
		$type = $this->_controller->request->header('Content-Type');
		return in_array($type, $this->_jsonTypes);
	}

	/**
	 * @access protected
	 * @return Boolean
	 */
	protected function checkRequestAcceptsHeader() {
		$accepts = $this->_controller->request->accepts();
		$intersect = is_array($accepts)
		           ? array_intersect($accepts, $this->_jsonTypes)
		           : array();
		return !empty($intersect);
	}

	/**
	 * @access protected
	 * @return Boolean
	 */
	protected function checkRequestExtension() {
		$bits = explode('.', $this->_controller->request->url);
		return $bits[count($bits) - 1] === 'json';
	}
}

