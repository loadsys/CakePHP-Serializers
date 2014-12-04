<?php
/**
 * AnalyzeRequest
 *
 * @package  Serializers.Lib
 */
class AnalyzeRequest {

	/**
	 * the request being processed
	 *
	 * @var Object $request
	 */
	protected $request;

	/**
	 * construct a new instance of the AnalyzeRequest class, set the passed in
	 * request to the _request property
	 *
	 * @param CakeRequest $request the CakeRequest object
	 */
	public function __construct($request) {
		$this->request = $request;
	}

	/**
	 * Checks various aspects of the request supplied on initialization
	 * to determine if the request is JSON.
	 *
	 * @return bool
	 */
	public function isJSON() {
		return $this->contentType() || $this->accepts() || $this->extension();
	}

	/**
	 * returns true only if the content type is application/json
	 *
	 * @return bool
	 */
	protected function contentType() {
		return $this->request->header('Content-Type') === 'application/json';
	}

	/**
	 * returns true if the request accepts header, will accept json
	 *
	 * @return bool
	 */
	protected function accepts() {
		$accepts = (array)$this->request->accepts();
		return array_reduce($accepts, function ($prev, $i) {
			return $prev ? $prev : preg_match('/^application\/.*json$/i', trim($i));
		}, false);
	}

	/**
	 * validates that the extension for the request is json
	 *
	 * @return bool
	 */
	protected function extension() {
		$bits = explode('.', $this->request->url);
		return $bits[count($bits) - 1] === 'json';
	}
}

