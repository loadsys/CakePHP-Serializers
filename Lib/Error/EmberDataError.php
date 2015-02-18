<?php
/**
 * Ensures that all errors encountered by the Cake app are returned as JSON
 * when debugging is enabled and HTTP codes only when it is disabled.
 */
App::uses('Debugger', 'Utility');
App::uses('CakeLog', 'Log');
App::uses('ErrorHandler', 'Error');

/**
 * EmberDataError
 */
class EmberDataError extends ErrorHandler {

	/**
	 * Set as the default exception handler by the CakePHP bootstrap process.
	 *
	 * This will either use custom exception renderer class if configured,
	 * or use the default ExceptionRenderer.
	 *
	 * @param Exception $exception The exception to render.
	 * @return void
	 * @see http://php.net/manual/en/function.set-exception-handler.php
	 */
	public static function handleException(Exception $exception) {
		$config = Configure::read('Exception');
		parent::_log($exception, $config);

		$renderer = isset($config['renderer']) ? $config['renderer'] : 'ExceptionRenderer';
		if ($renderer !== 'ExceptionRenderer') {
			list($plugin, $renderer) = pluginSplit($renderer, true);
			App::uses($renderer, $plugin . 'Error');
		}
		try {
			$error = new $renderer($exception);
			$error->render();
		} catch (Exception $e) {
			set_error_handler(Configure::read('Error.handler')); // Should be using configured ErrorHandler
			Configure::write('Error.trace', false); // trace is useless here since it's internal
			$message = sprintf("[%s] %s\n%s", // Keeping same message format
				get_class($e),
				$e->getMessage(),
				$e->getTraceAsString()
			);
			trigger_error($message, E_USER_ERROR);
		}
	}

	/**
	 * A slightly modified version of Cake.Error ErrorHandler::handleError() that
	 * logs everything no matter what the debug level is.
	 *
	 * @param int    $code Code of error
	 * @param string $description Error description
	 * @param string $file File on which error occurred
	 * @param int    $line Line that triggered the error
	 * @param array  $context Context
	 * @return bool true if error was handled
	 */
	public static function handleError($code, $description, $file = null, $line = null, $context = null) {
		if (error_reporting() === 0) {
			return false;
		}
		$errorConfig = Configure::read('Error');
		list($error, $log) = ErrorHandler::mapErrorCode($code);
		if ($log === LOG_ERR) {
			return self::handleFatalError($code, $description, $file, $line);
		}

		$message = $error . ' (' . $code . '): ' . $description . ' in [' . $file . ', line ' . $line . ']';
		if (!empty($errorConfig['trace'])) {
			$trace = Debugger::trace(array('start' => 1, 'format' => 'log'));
			$message .= "\nTrace:\n" . $trace . "\n";
		}

		$debug = Configure::read('debug');
		if ($debug) {
			$data = array(
				'level' => $log,
				'code' => $code,
				'error' => $error,
				'description' => $description,
				'file' => $file,
				'line' => $line,
				'context' => $context,
				'start' => 2,
				'path' => Debugger::trimPath($file)
			);
			CakeLog::write($log, $message);
			return Debugger::getInstance()->outputError($data);
		}
		return CakeLog::write($log, $message);
	}

	/**
	 * Generate an error page when a fatal error happens.
	 * (from Cake.Error ErrorHandler::handleFatalError())
	 *
	 * @param int    $code Code of error
	 * @param string $description Error description
	 * @param string $file File on which error occurred
	 * @param int    $line Line that triggered the error
	 * @return bool
	 */
	public static function handleFatalError($code, $description, $file, $line) {
		$logMessage = 'Fatal Error (' . $code . '): ' . $description . ' in [' . $file . ', line ' . $line . ']';
		CakeLog::write(LOG_ERR, $logMessage);

		$exceptionHandler = Configure::read('Exception.handler');
		if (!is_callable($exceptionHandler)) {
			return false;
		}

		if (ob_get_level()) {
			ob_end_clean();
		}

		if (Configure::read('debug')) {
			call_user_func($exceptionHandler, new FatalErrorException($description, 500, $file, $line));
		} else {
			call_user_func($exceptionHandler, new InternalErrorException());
		}
		return false;
	}

}
