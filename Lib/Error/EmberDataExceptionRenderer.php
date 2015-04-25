<?php
/**
 * Ensures that all fatal errors are rendered as JSON.
 */
App::uses('ExceptionRenderer', 'Error');
App::uses('Inflector', 'Utility');
App::uses('Hash', 'Utility');

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
				$this->controller->set('errors', $this->getErrorData());
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
	 * process validation error messages that occur
	 *
	 * @param array $requestData the request data from the controller
	 * @param array $errorData the validation error messages from the model
	 * @param string $modelName the name of the model
	 * @return array
	 */
	public function processErrors($requestData, $errorData, $modelName) {
		$errorOutputData = array();

		foreach ($requestData[$modelName] as $index => $value) {
			if (
				is_array($value)
				&& !is_int($index)
				&& array_key_exists($index, $errorData)
			) {
				// this is a secondary-model
				$secondaryModelName = $index;
				$secondaryModelJsonKey = Inflector::tableize($index);
				$secondaryModelData[$secondaryModelName] = $value;
				$secondaryModelError[$secondaryModelName] = $errorData[$secondaryModelName];
				$secondaryModelErrorOutput = $this->processErrors($secondaryModelData, $secondaryModelError, $secondaryModelName);

				$errorOutputData[$secondaryModelJsonKey] = $secondaryModelErrorOutput;
			} elseif (
				is_int($index)
			) {
				$errorOutputData[$index] = array();
				// this is looping through a secondary model
				foreach ($errorData[$modelName] as $keyWithError => $errorsForObjectAtKey) {
					foreach ($errorsForObjectAtKey as $fieldWithError => $errorMessages) {
						// spec can't handle multiple failures per field, so only return the first found.
						$errorOutputData[$index][$fieldWithError] = $errorMessages;
					}
				}
			} elseif (
				array_key_exists($index, $errorData)
			) {
				$errorOutputData[$index] = $errorData[$index];
			} else {
				// this field for a top level model doesn't have errors
			}
		}

		return $errorOutputData;
	}

	/**
	 * Helper method used to generate extra debugging data into the error template
	 *
	 * @return array debugging data
	 */
	protected function getErrorData() {
		$data = array();

		$viewVars = $this->controller->viewVars;
		if (!empty($viewVars['_serialize'])) {
			foreach ($viewVars['_serialize'] as $v) {
				$data[$v] = $viewVars[$v];
			}
		}

		if ($viewVars['error'] instanceof ValidationFailedJsonApiException) {
			if (!empty($viewVars['error'])) {
				$errorData = $viewVars['error']->getDetail();
				$requestData = $viewVars['error']->getRequestData();

				$topLevelModel = key($requestData);
				$errorOutputData = $this->processErrors($requestData, $errorData, $topLevelModel);

				foreach ($errorData as $fieldWithError => $errorMessages) {
					$firstErrorMessage = reset($errorMessages);
					// this is an error of a sub model attempted to be saved at the same
					// time as the primary model if error messages is an array of arrays
					if (is_array($firstErrorMessage)) {
						$secondaryModelName = Inflector::tableize($fieldWithError);

						// // if the number of models fialed
						$numberOfModelsFailedValidation = count($errorMessages);
						end($errorMessages);
						$finalKey = (key($errorMessages) + 1);

						reset($errorMessages);

						// if the final key in our array of error messages is different
						// from the number of models that failed validation
						// we need to populated an empty object at each and every
						// instance of the secondary model that exists on the data passed
						// to the save method
						$errorOutputData[$secondaryModelName] = array_fill(0, $finalKey, new stdClass());

						foreach ($errorMessages as $indexOfError => $fieldErrors) {
							foreach ($fieldErrors as $fieldName => $errorMessages) {
								if ($errorOutputData[$secondaryModelName][$indexOfError] instanceof stdClass) {
									$errorOutputData[$secondaryModelName][$indexOfError] = array();
								}
								$errorOutputData[$secondaryModelName][$indexOfError][$fieldName] = $errorMessages;
							}
						}
					} else {
						$errorOutputData[$fieldWithError] = $errorMessages;
					}
				}

				return $errorOutputData;
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
