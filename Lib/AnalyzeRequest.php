<?php

/**
 * AnalyzeRequest
 */
class AnalyzeRequest {
	/**
	 * @access protected
	 * @var Object $_request
	 */
	protected $_request;

	/**
	 * Fill me in.
	 *
	 * @access public
	 * @param CakeRequest $request
	 */
	public function __construct($request) {
		$this->_request = $request;
	}

	/**
	 * Checks various aspects of the request supplied on initialization
	 * to determine if the request is JSON.
	 *
	 * @access public
	 * @return Boolean
	 */
	public function isJSON() {
		return $this->contentType() || $this->accepts() || $this->extension();
	}

	/**
	 * @access protected
	 * @return Boolean
	 */
	protected function contentType() {
		return $this->_request->header('Content-Type') === 'application/json';
	}

	/**
	 * @access protected
	 * @return Boolean
	 */
	protected function accepts() {
		$accepts = (array) $this->_request->accepts();
		return array_reduce($accepts, function($prev, $i) {
			return $prev ? $prev : preg_match('/^application\/.*json$/i', trim($i));
		}, false);
	}

	/**
	 * @access protected
	 * @return Boolean
	 */
	protected function extension() {
		$bits = explode('.', $this->_request->url);
		return $bits[count($bits) - 1] === 'json';
	}
}

