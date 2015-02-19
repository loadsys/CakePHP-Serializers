<?php
App::uses('Controller', 'Controller');
App::uses('Router', 'Routing');
App::uses('EmberDataError', 'Serializers.Error');
App::uses('ExceptionRenderer', 'Error');

/**
 * ExceptionThrowerRenderer class
 *
 * Acts as a renderer that will always throw an exception on calls to
 * ::render(), allowing testing of branches in EmberDataError::handleException().
 *
 */
class ExceptionThrowerRenderer extends ExceptionRenderer {
	public function render() {
		throw new Exception('I always throw an exception.');
	}
}

/**
 * EmberDataErrorTest class
 *
 * Test methods adapted from Cake.Test.Case.Error ErrorHandlerTest.php
 *
 */
class EmberDataErrorTest extends CakeTestCase {

	/**
	 * Preserve PHPUnit's fatal error to exception conversion settings.
	 *
	 * Set `$this->_restoreError = true;` in your test method to have
	 * ::tearDown() restore the previous setting.
	 *
	 * @var array[bool]
	 */
	protected $errorsToExceptions = array();


	/**
	 * Preserve PHP's error_reporting() level.
	 *
	 * Set `$this->_restoreError = true;` in your test method to have
	 * ::tearDown() restore the previous setting.
	 *
	 * @var int
	 */
	protected $errorReporting = null;

	/**
	 * Flag ::tearDown() to restore various PHP error handling settings.
	 *
	 * @var bool
	 */
	protected $_restoreError = false;

	/**
	 * setup create a request object to get out of router later.
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		// Backup error_reporting.
		$this->errorReporting = error_reporting();

		// Backup phpunit fatal-error-to-exception conversion settings.
		$this->errorsToExceptions['warning'] = PHPUnit_Framework_Error_Warning::$enabled;
		PHPUnit_Framework_Error_Warning::$enabled = true;

		App::build(array(
			'View' => array(
				APP . 'View' . DS
			)
		), App::RESET);
		Router::reload();

		$request = new CakeRequest(null, false);
		$request->base = '';
		Router::setRequestInfo($request);
		Configure::write('debug', 2);

		CakeLog::disable('error');
		CakeLog::disable('debug');
		CakeLog::disable('stdout');
		CakeLog::disable('stderr');
		ob_start();
	}

	/**
	 * tearDown
	 *
	 * @return void
	 */
	public function tearDown() {
		ob_end_clean();
		parent::tearDown();
		if ($this->_restoreError) {
			error_reporting($this->errorReporting);
			restore_error_handler();
			PHPUnit_Framework_Error_Warning::$enabled = $this->errorsToExceptions['warning'];
		}
		CakeLog::enable('error');
		CakeLog::enable('debug');
		CakeLog::enable('stdout');
		CakeLog::enable('stderr');
	}

	/**
	 * test error handling when debug is on, an error should be printed from Debugger.
	 *
	 * @return void
	 */
	public function testHandleErrorDebugOn() {
		$this->_restoreError = true;

		$code = 42;
		$description = 'wrong';
		$context = 'undefined variable';
		ob_start();
		EmberDataError::handleError($code, $description, __FILE__, __LINE__, $context);
		$result = ob_get_clean();

		$this->assertRegExp('/42/', $result);
		$this->assertRegExp('/undefined variable/', $result);
		$this->assertRegExp('/wrong/', $result);
	}

	/**
	 * Test error handling when error maps to LOG_ERROR.
	 *
	 * @return void
	 */
	public function testHandleErrorFatalAndNoHandler() {
		Configure::write('Exception.handler', 'thisIsNotCallable');
		$this->_restoreError = true;

		$code = E_USER_ERROR; // Triggers ::handleFatalError() call.
		$description = 'wrong';
		$context = 'undefined variable';
		$this->assertFalse(
			EmberDataError::handleError($code, $description, __FILE__, __LINE__, $context),
			'The sub-call to ::handleFatalError() will return false because no Exception.handler is Configured.'
		);
	}

	/**
	 * Test error handling when error_reporting is off.
	 *
	 * @return void
	 */
	public function testHandleErrorWithErrorReportingOff() {
		error_reporting(0);
		$this->_restoreError = true;

		$code = null;
		$description = 'does not matter';
		$context = '';
		$this->assertFalse(
			EmberDataError::handleError($code, $description, __FILE__, __LINE__, $context),
			'No output is expected when error_reporting is turned off.'
		);
	}

	/**
	 * Test that errors go into CakeLog when debug = 0.
	 *
	 * @return void
	 */
	public function testHandleErrorDebugOff() {
		Configure::write('debug', 0);
		Configure::write('Error.trace', false);
		if (file_exists(LOGS . 'debug.log')) {
			unlink(LOGS . 'debug.log');
		}

		set_error_handler('EmberDataError::handleError');
		$this->_restoreError = true;

		$out = ''; // Intentionally wrong. This triggers the error. @TODO: Could also just be simulated by calling EmberDataError::handleError() directly. (We don't care that Cake tirggers the code correctly, only that OUR code does the correct thing with the arguments provided.) Ref: http://blog.pixelastic.com/2010/08/08/testing-a-custom-errorhandler-in-cakephp/

		$result = file(LOGS . 'debug.log');
		$this->assertEquals(1, count($result));
		$this->assertRegExp(
			'/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2} (Notice|Debug): Notice \(8\): Undefined variable:\s+out in \[.+ line \d+\]$/',
			$result[0]
		);
		if (file_exists(LOGS . 'debug.log')) {
			unlink(LOGS . 'debug.log');
		}
	}

	/**
	 * Test that errors go into CakeLog when debug > 0.
	 *
	 * @return void
	 */
	public function testHandleErrorDebugOnLogError() {
		Configure::write('debug', 1);
		if (file_exists(LOGS . 'debug.log')) {
			unlink(LOGS . 'debug.log');
		}

		set_error_handler('EmberDataError::handleError');
		$this->_restoreError = true;

		$out .= ''; // Intentionally wrong. This triggers the error. @TODO: Could also just be simulated by calling EmberDataError::handleError() directly. (We don't care that Cake tirggers the code correctly, only that OUR code does the correct thing with the arguments provided.) Ref: http://blog.pixelastic.com/2010/08/08/testing-a-custom-errorhandler-in-cakephp/

		$result = file(LOGS . 'debug.log');
		//$this->assertEquals(1, count($result));
		$this->assertRegExp(
			'/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2} (Notice|Debug): Notice \(8\): Undefined variable:\s+out in \[.+ line \d+\]$/',
			$result[0]
		);
		if (file_exists(LOGS . 'debug.log')) {
			unlink(LOGS . 'debug.log');
		}
	}

	/**
	 * Test that errors going into CakeLog include traces.
	 *
	 * @return void
	 */
	public function testHandleErrorLoggingTrace() {
		Configure::write('debug', 0);
		Configure::write('Error.trace', true);
		if (file_exists(LOGS . 'debug.log')) {
			unlink(LOGS . 'debug.log');
		}

		set_error_handler('EmberDataError::handleError');
		$this->_restoreError = true;

		$out .= ''; // Intentionally wrong. This triggers the error. @TODO: Could also just be simulated by calling EmberDataError::handleError() directly. (We don't care that Cake tirggers the code correctly, only that OUR code does the correct thing with the arguments provided.) Ref: http://blog.pixelastic.com/2010/08/08/testing-a-custom-errorhandler-in-cakephp/

		$result = file(LOGS . 'debug.log');
		$this->assertRegExp(
			'/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2} (Notice|Debug): Notice \(8\): Undefined variable:\s+out in \[.+ line \d+\]$/',
			$result[0]
		);
		$this->assertRegExp('/^Trace:/', $result[1]);
		$this->assertRegExp('/^EmberDataErrorTest\:\:testHandleErrorLoggingTrace\(\)/', $result[2]);
		if (file_exists(LOGS . 'debug.log')) {
			unlink(LOGS . 'debug.log');
		}
	}

	/**
	 * test handleFatalError generating a page.
	 *
	 * These tests start two buffers as handleFatalError blows the outer one up.
	 *
	 * @return void
	 */
	public function testHandleFatalErrorPage() {
		ob_start();
		ob_start();
		Configure::write('debug', 1);
		$line = __LINE__; EmberDataError::handleFatalError(E_ERROR, 'Something wrong', __FILE__, $line);  // Must be on the same line for accuracy.
		$result = ob_get_clean();

		$this->assertContains('Something wrong', $result, 'message missing.');
		$this->assertContains(__FILE__, stripslashes($result), 'filename missing.');
		$this->assertContains((string)$line, $result, 'line missing.');


		ob_start();
		ob_start();
		Configure::write('debug', 0);
		$line = __LINE__; EmberDataError::handleFatalError(E_ERROR, 'Something wrong', __FILE__, $line);  // Must be on the same line for accuracy.
		$result = ob_get_clean();

		$this->assertNotContains('Something wrong', $result, 'message must not appear.');
		$this->assertNotContains(__FILE__, $result, 'filename must not appear.');
		$this->assertEmpty($result);
	}

	/**
	 * Test handleException().
	 *
	 * Ensures that when the renderer itself throws an Exception, we handle
	 * the result.
	 *
	 * @return void
	 */
	public function testHandleException() {
		Configure::write('Error.handler', 'EmberDataErrorTest::handleExceptionHandler');
		Configure::write('Exception.renderer', 'ExceptionThrowerRenderer');
		$this->_restoreError = true;

		EmberDataError::handleException(new Exception('blah'));
	}

	/**
	 * Counterpart to testHandleException.
	 *
	 * This method is assigned as PHP's error handler in testHandleException()
	 * above. It will be called when the assigned Exception.renderer
	 * `ExceptionThrowerRenderer::render()` throws an exception. All we need
	 * to do is assert that we get the expected message.
	 *
	 * @return void
	 */
	public static function handleExceptionHandler($errno, $errstr, $errfile = null, $errline = null, $errcontext = null) {
		self::assertContains(
			'[Exception] I always throw an exception.',
			$errstr,
			'The exception we catch here should be the one thrown from our ExceptionThrowerRenderer::render() method.'
		);
		return true;
	}
}
