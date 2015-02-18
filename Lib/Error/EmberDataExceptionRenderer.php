<?php
/**
 * Ensures that all fatal errors are rendered as JSON.
 */
App::uses('ExceptionRenderer', 'Error');

/**
 * EmberDataExceptionRenderer
 */
class EmberDataExceptionRenderer extends ExceptionRenderer {

	/**
	 * Generate the response using the controller object.
	 *
	 * Sets the viewclass to disable CakeSerializers and sets the controller
	 * response type to JSON. If debug is > 0, the full error
	 * message is serialized and sent, along with the HTTP status code. If
	 * debug == 0, only the HTTP status code is sent.
	 *
	 * @param string $template the template being used to render Exception
	 * @return void
	 */
	protected function _outputMessage($template) {
		// Disable CakeSerializers
		$this->controller->viewClass = 'Json';
		// Set the response type to JSON even if only sending HTTP status code.
		$this->controller->response->type('json');
		try {
			if (Configure::read('debug') > 0) {
				$this->controller->set('errors', $this->_getErrorData());
				$this->controller->set('_serialize', array('errors'));
				$this->controller->render($template);
				$this->controller->afterFilter();
			}
			$this->controller->response->send();
		} catch (MissingViewException $e) {
			$this->_outputMessageSafe('error500');
		} catch (BadRequestException $e) {
			$this->_outputMessageSafe('error400');
		} catch (Exception $e) {
			$this->controller->set(array(
				'error' => $e,
				'name' => $e->getMessage(),
				'code' => $e->getCode()
			));
			$this->_outputMessageSafe('error500');
		}
	}

	/**
	 * A safer way to render error messages, replaces all helpers, with basics
	 * and doesn't call component methods.
	 *
	 * @param string $template the template being used to render Exception
	 * @return void
	 */
	protected function _outputMessageSafe($template = null) {
		// Disable CakeSerializers
		$this->controller->viewClass = 'Json';
		// Set the response type to JSON even if only sending HTTP status code.
		$this->controller->response->type('json');

		$this->controller->layoutPath = null;
		$this->controller->subDir = null;
		$this->controller->viewPath = null;
		$this->controller->layout = null;
		$this->controller->helpers = array();

		$error = array();

		if (Configure::read('debug') > 0) {
			switch ($template) {
				case 'error400':
					$error['title'] = 'Not Found Error';
					$error['code'] = 'Not Found Error';
					$error['status'] = '400';
					break;
				case 'error500':
				default:
					$error['title'] = 'Internal Server Error';
					$error['code'] = 'InternalServerError';
					$error['status'] = '500';
			}

			$data[] = $error;

			$this->controller->set('errors', $data);
			$this->controller->set('_serialize', array('errors'));
			$this->controller->render($template);
		}
		$this->controller->response->send();
	}

	/**
	 * Helper method used to generate extra debugging data into the error template
	 *
	 * @return array debugging data
	 */
	protected function _getErrorData() {
		$data = array();

		$viewVars = $this->controller->viewVars;
		if (!empty($viewVars['_serialize'])) {
			foreach ($viewVars['_serialize'] as $v) {
				$data[$v] = $viewVars[$v];
			}
		}

		if ($viewVars['error'] instanceof ValidationFailedJsonApiException) {
			if (!empty($viewVars['error'])) {
				$data = $viewVars['error']->getDetail();
				foreach ($data as $errorVar => $errorDescrptions) {
					$data[$errorVar] = $errorDescrptions[0];
				}
				return $data;
			}
		} elseif ($viewVars['error'] instanceof StandardJsonApiExceptions) {
			if (!empty($viewVars['error'])) {
				$data = array(
					'id' => $viewVars['error']->getId(),
					'href' => $viewVars['error']->getHref(),
					'status' => $viewVars['error']->getCode(),
					'code' => get_class($viewVars['error']),
					'title' => $viewVars['error']->getMessage(),
					'detail' => $viewVars['error']->getDetail(),
					'current_url' => $this->controller->request->here,
				);
			}
		} else {
			if (!empty($viewVars['error'])) {
				$data = array(
					'code' => get_class($viewVars['error']),
					'status' => $viewVars['error']->getCode(),
					'title' => $viewVars['error']->getMessage(),
					'current_url' => $this->controller->request->here,
				);
			}

			if (Configure::read('debug')) {
				$data['detail'] = $viewVars['error']->getTraceAsString();
			}
		}

		$return[] = $data;

		return $return;
	}

	/**
	 * Cake's default _getController() method will use AppController if it's
	 * available. In our case, this introduces stateless HTTP header checks
	 * for a `Bearer` authentication token, which obviously won't exist in
	 * the self-contained microcosm of the error rendering process. So we
	 * override that method to perform similarly, just without the
	 * AppController (skpping straight to Controller instead.)
	 *
	 * @param Exception $exception The exception to get a controller for.
	 * @return Controller
	 */
	protected function _getController($exception) {
		if (!$request = Router::getRequest(true)) {
			$request = new CakeRequest();
		}

		$response = new CakeResponse();
		$addedHttpCodes = array(
			422 => 'Unprocessable Entity',
		);
		$response->httpCodes($addedHttpCodes);

		if (method_exists($exception, 'responseHeader')) {
			$response->header($exception->responseHeader());
		}
		$controller = new Controller($request, $response);
		$controller->viewPath = 'Errors';
		return $controller;
	}
}
