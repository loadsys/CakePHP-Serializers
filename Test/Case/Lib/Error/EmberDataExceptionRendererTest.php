<?php
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('EmberDataExceptionRenderer', 'Serializers.Error');
App::uses('ConnectionManager', 'Model');

/**
 * TestEmberDataExceptionRenderer
 *
 * Exposes protected methods for more direct testing.
 *
 */
class TestEmberDataExceptionRenderer extends EmberDataExceptionRenderer {
	public function _getController($exception) {
		return parent::_getController($exception);
	}
	public function _getErrorData() {
		return parent::_getErrorData();
	}
}


/**
 * EmberDataExceptionRendererTest
 *
 */
class EmberDataExceptionRendererTest extends CakeTestCase {

	/**
	 * Flag ::tearDown() to restore various PHP error handling settings.
	 *
	 * @var bool
	 */
	public $fixtures = array('core.post');

	/**
	 * setUp
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
	}

	/**
	 * tearDown
	 *
	 * @return void
	 */
	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * Test the guts of _getController() when no CakeRequest is available.
	 *
	 * @return void
	 */
	public function testGetControllerWhenNoRequest() {
		// Make sure there are no requests left in the Router singleton.
		do {
			$req = Router::popRequest();
		} while (!is_null($req));

		$e = new Exception('blah');
		$renderer = new TestEmberDataExceptionRenderer($e);
		$controller = $renderer->_getController($e);

		$this->assertInstanceOf(
			'Controller',
			$controller,
			'Returned object must be a controller.'
		);
		$this->assertInstanceOf(
			'CakeRequest',
			$controller->request,
			'The ::request property must be a CakeRequest.'
		);
		$this->assertInstanceOf(
			'CakeResponse',
			$controller->response,
			'The ::response property must be a CakeResponse.'
		);
		$this->assertEquals(
			'Errors',
			$controller->viewPath,
			'View path must be set to Errors subdirectory.'
		);
	}

	/**
	 * Test the guts of _getErrorData() when the Exception is an instance
	 * of JsonApiException.
	 *
	 * @return void
	 */
	public function testGetErrorDataForJsonApiException() {
		$title = 'blah';
		$detail = 'woah bad thing happened';
		$code = 444;
		$href = '/right/here';
		$id = 12345;
		$expected = array(
				'id' => 12345,
				'href' => $href,
				'status' => $code,
				'code' => 'StandardJsonApiExceptions',
				'title' => $title,
				'detail' => $detail,
		);

		$e = new StandardJsonApiExceptions($title, $detail, $code, $href, $id);
		$renderer = new TestEmberDataExceptionRenderer($e);
		$renderer->controller->viewVars = array('error' => $e);

		$result = $renderer->_getErrorData();

		foreach ($expected as $k => $v) {
			$this->assertEquals(
				$v,
				$result[0][$k],
				"The data prepared for a StandardJsonApiExceptions in key $k should match our expectation."
			);
		}
	}

	/**
	 * Test the guts of _getErrorData() when the Exception is an instance
	 * of ValidationFailedJsonApiException.
	 *
	 * @return void
	 */
	public function testGetErrorDataForValidationFailedJsonApiException() {
		$title = 'Something failed validation';
		$detail = array(
			'name' => array(
				0 => 'Name must not be empty.',
				1 => 'Name must not be empty.'
			)
		);
		$expected = array(
			'name' => 'Name must not be empty.',
		);

		$e = new ValidationFailedJsonApiException($title, $detail);
		$renderer = new TestEmberDataExceptionRenderer($e);
		$renderer->controller->viewVars = array('error' => $e);

		$result = $renderer->_getErrorData();

		$this->assertEquals(
			$expected,
			$result,
			"The result for the _getErrorData call did not match the expected result"
		);
	}

	/**
	 * Test basic normal rendering.
	 *
	 * @return void
	 */
	public function testNormalExceptionRendering() {
		Configure::write('debug', 1);
		$Exception = new CakeException('Hello World');

		$Controller = $this->getMock('Controller', array('render'));
		$Controller->request = new CakeRequest();
		$Controller->response = new CakeResponse();

		$Renderer = $this->getMock('EmberDataExceptionRenderer', array('_getController'), array(), '', false);
		$Renderer
			->expects($this->once())
			->method('_getController')
			->with($Exception)
			->will($this->returnValue($Controller));

		$Renderer->__construct($Exception);
		$Renderer->render();

		$viewVars = $Controller->viewVars;

		$this->assertTrue(!empty($viewVars['_serialize']));

		$expected = array('errors');
		$actual = $viewVars['_serialize'];
		$this->assertSame($expected, $actual);

		$expected = array(
			'errors' => array(
				array(
					'code' => 'CakeException',
					'status' => 500,
					'title' =>'Hello World',
					'detail' => null,
				),
			),
		);

		$this->assertTrue(!isset($actual['errors']));
		$this->assertTrue(isset($viewVars['errors'][0]));
		$this->assertTrue(is_array($viewVars['errors'][0]));

		$this->assertTrue(isset($viewVars['errors'][0]['code']));
		$this->assertEquals('CakeException', $viewVars['errors'][0]['code']);

		$this->assertTrue(isset($viewVars['errors'][0]['status']));
		$this->assertEquals('500', $viewVars['errors'][0]['status']);

		$this->assertTrue(isset($viewVars['errors'][0]['title']));
		$this->assertEquals('Hello World', $viewVars['errors'][0]['title']);

		$this->assertTrue(isset($viewVars['errors'][0]['current_url']));
	}

	/**
	 * Test normal rendering, with query log.
	 *
	 * @return void
	 */
	public function testNormalExceptionRenderingQueryLog() {
		Configure::write('debug', 2);
		$Exception = new CakeException('Hello World');

		$Controller = $this->getMock('Controller', array('render'));
		$Controller->request = new CakeRequest();
		$Controller->response = new CakeResponse();

		$Renderer = $this->getMock('EmberDataExceptionRenderer', array('_getController'), array(), '', false);
		$Renderer
			->expects($this->once())
			->method('_getController')
			->with($Exception)
			->will($this->returnValue($Controller));

		$Renderer->__construct($Exception);
		$Renderer->render();

		$viewVars = $Controller->viewVars;

		$this->assertTrue(!empty($viewVars['_serialize']));

		$expected = array('errors');
		$actual = $viewVars['_serialize'];
		$this->assertSame($expected, $actual);

		$expected = array(
			'errors' => array(
				array(
					'code' => 'CakeException',
					'status' => 500,
					'title' =>'Hello World',
					'detail' => null,
				),
			),
		);

		$this->assertTrue(!isset($actual['errors']));
		$this->assertTrue(isset($viewVars['errors'][0]));
		$this->assertTrue(is_array($viewVars['errors'][0]));

		$this->assertTrue(isset($viewVars['errors'][0]['code']));
		$this->assertEquals('CakeException', $viewVars['errors'][0]['code']);

		$this->assertTrue(isset($viewVars['errors'][0]['status']));
		$this->assertEquals('500', $viewVars['errors'][0]['status']);

		$this->assertTrue(isset($viewVars['errors'][0]['title']));
		$this->assertEquals('Hello World', $viewVars['errors'][0]['title']);

		$this->assertTrue(isset($viewVars['errors'][0]['current_url']));
	}

	/**
	 * Test normal rendering, nested.
	 *
	 * @return void
	 */
	public function testNormalNestedExceptionRendering() {
		Configure::write('debug', 1);
		$Exception = new CakeException('Hello World');

		$Controller = $this->getMock('Controller', array('render'));
		$Controller->request = new CakeRequest();
		$Controller->response = new CakeResponse();

		$Renderer = $this->getMock('EmberDataExceptionRenderer', array('_getController'), array(), '', false);
		$Renderer
			->expects($this->once())
			->method('_getController')
			->with($Exception)
			->will($this->returnValue($Controller));

		$Renderer->__construct($Exception);
		$Renderer->render();

		$viewVars = $Controller->viewVars;

		$this->assertTrue(!empty($viewVars['_serialize']));

		$expected = array('errors');
		$actual = $viewVars['_serialize'];
		$this->assertSame($expected, $actual);

		$expected = array(
			'errors' => array(
				array(
					'code' => 'CakeException',
					'status' => 500,
					'title' =>'Hello World',
					'detail' => null,
				),
			),
		);

		$this->assertTrue(!isset($actual['errors']));
		$this->assertTrue(isset($viewVars['errors'][0]));
		$this->assertTrue(is_array($viewVars['errors'][0]));

		$this->assertTrue(isset($viewVars['errors'][0]['code']));
		$this->assertEquals('CakeException', $viewVars['errors'][0]['code']);

		$this->assertTrue(isset($viewVars['errors'][0]['status']));
		$this->assertEquals('500', $viewVars['errors'][0]['status']);

		$this->assertTrue(isset($viewVars['errors'][0]['title']));
		$this->assertEquals('Hello World', $viewVars['errors'][0]['title']);

		$this->assertTrue(isset($viewVars['errors'][0]['current_url']));
	}

	/**
	 * Test a bad request.
	 *
	 * @return void
	 */
	public function testBadRequestExceptionDuringRendering() {
		Configure::write('debug', 1);
		$Exception = new BadRequestException('Hello World');

		$Controller = $this->getMock('Controller', array('render'));
		$Controller->request = new CakeRequest();
		$Controller->response = $this->getMock('CakeResponse', array('send'));
		$Controller->response
			->expects($this->at(0))
			->method('send')
			->will($this->throwException(new BadRequestException('boo')));

		$Renderer = $this->getMock('EmberDataExceptionRenderer', array('_getController'), array(), '', false);
		$Renderer
			->expects($this->once())
			->method('_getController')
			->with($Exception)
			->will($this->returnValue($Controller));

		$Renderer->__construct($Exception);
		$Renderer->render();

		$viewVars = $Controller->viewVars;

		$this->assertTrue(!empty($viewVars['_serialize']));

		$expected = array('errors');
		$actual = $viewVars['_serialize'];
		$this->assertSame($expected, $actual);

		$expected = array(
			'errors' => array(
				array(
					'code' => 'Not Found Error',
					'status' => 400,
					'title' =>'Not Found Error',
					'detail' => null,
				),
			),
		);

		$this->assertTrue(!isset($actual['errors']));
		$this->assertTrue(isset($viewVars['errors'][0]));
		$this->assertTrue(is_array($viewVars['errors'][0]));

		$this->assertTrue(isset($viewVars['errors'][0]['code']));
		$this->assertEquals($expected['errors'][0]['code'], $viewVars['errors'][0]['code']);

		$this->assertTrue(isset($viewVars['errors'][0]['status']));
		$this->assertEquals($expected['errors'][0]['status'], $viewVars['errors'][0]['status']);

		$this->assertTrue(isset($viewVars['errors'][0]['title']));
		$this->assertEquals($expected['errors'][0]['title'], $viewVars['errors'][0]['title']);
	}

	/**
	 * Test encountering a missing view during the rendering process.
	 *
	 * @return void
	 */
	public function testMissingViewExceptionDuringRendering() {
		Configure::write('debug', 1);
		$Exception = new CakeException('Hello World');

		$Controller = $this->getMock('Controller', array('render'));
		$Controller->request = new CakeRequest();
		$Controller->response = $this->getMock('CakeResponse', array('send'));
		$Controller->response
			->expects($this->at(0))
			->method('send')
			->will($this->throwException(new MissingViewException('boo')));

		$Renderer = $this->getMock('EmberDataExceptionRenderer', array('_getController'), array(), '', false);
		$Renderer
			->expects($this->once())
			->method('_getController')
			->with($Exception)
			->will($this->returnValue($Controller));

		$Renderer->__construct($Exception);
		$Renderer->render();

		$viewVars = $Controller->viewVars;

		$this->assertTrue(!empty($viewVars['_serialize']));

		$expected = array('errors');
		$actual = $viewVars['_serialize'];
		$this->assertSame($expected, $actual);

		$expected = array(
			'errors' => array(
				array(
					'code' => 'InternalServerError',
					'status' => 500,
					'title' =>'Internal Server Error',
					'detail' => null,
				),
			),
		);

		$this->assertTrue(!isset($actual['errors']));
		$this->assertTrue(isset($viewVars['errors'][0]));
		$this->assertTrue(is_array($viewVars['errors'][0]));

		$this->assertTrue(isset($viewVars['errors'][0]['code']));
		$this->assertEquals('InternalServerError', $viewVars['errors'][0]['code']);

		$this->assertTrue(isset($viewVars['errors'][0]['status']));
		$this->assertEquals('500', $viewVars['errors'][0]['status']);

		$this->assertTrue(isset($viewVars['errors'][0]['title']));
		$this->assertEquals('Internal Server Error', $viewVars['errors'][0]['title']);
	}

	/**
	 * Test encountering an Exception during the rendering process.
	 *
	 * @return void
	 */
	public function testGenericExceptionDuringRendering() {
		Configure::write('debug', 1);

		$Exception = new CakeException('Hello World');
		$NestedException = new CakeException('Generic Exception Description');

		$Controller = $this->getMock('Controller', array('render'));
		$Controller->request = new CakeRequest();
		$Controller->response = $this->getMock('CakeResponse', array('send'));
		$Controller->response
			->expects($this->at(0))
			->method('send')
			->will($this->throwException($NestedException));

		$Renderer = $this->getMock('EmberDataExceptionRenderer', array('_getController'), array(), '', false);
		$Renderer
			->expects($this->once())
			->method('_getController')
			->with($Exception)
			->will($this->returnValue($Controller));

		$Renderer->__construct($Exception);
		$Renderer->render();

		$viewVars = $Controller->viewVars;

		$this->assertTrue(!empty($viewVars['_serialize']));

		$expected = array('errors');
		$actual = $viewVars['_serialize'];
		$this->assertSame($expected, $actual);

		$expected = array(
			'errors' => array(
				array(
					'code' => 'InternalServerError',
					'status' => 500,
					'title' =>'Internal Server Error',
					'detail' => null,
				),
			),
		);

		$this->assertTrue(!isset($actual['errors']));
		$this->assertTrue(isset($viewVars['errors'][0]));
		$this->assertTrue(is_array($viewVars['errors'][0]));

		$this->assertTrue(isset($viewVars['errors'][0]['code']));
		$this->assertEquals('InternalServerError', $viewVars['errors'][0]['code']);

		$this->assertTrue(isset($viewVars['errors'][0]['status']));
		$this->assertEquals('500', $viewVars['errors'][0]['status']);

		$this->assertTrue(isset($viewVars['errors'][0]['title']));
		$this->assertEquals('Internal Server Error', $viewVars['errors'][0]['title']);
	}
}
