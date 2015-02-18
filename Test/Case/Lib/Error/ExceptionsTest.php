<?php
// Load CakePHP Serializers Exceptions
App::import('Lib/Error', 'Serializers.StandardJsonApiExceptions');

/**
 * Exceptions tests
 *
 */
class StandardJsonApiExceptionsTest extends CakeTestCase {

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
	 * Confirm that all Exception constructors set provided args into the
	 * correct properties. As a side-effect, also tests the getter methods
	 * for all properties.
	 *
	 * @dataProvider	provideTestConstructorsArgs
	 * @param	string	$class		The Exception class name to instantiate.
	 * @param	array	$args		An array of args to pass to the constructor method.
	 * @param	string	$msg		Optional PHPUnit error message when the assertion fails.
	 * @return void
	 */
	public function testExceptionConstructors($class, $args, $expected, $msg = 'The method ::%1$s() is expected to return the value `%2$s`.') {
		extract($args);
		$e = new $class($title, $detail, $code, $href, $id);
		foreach ($expected as $method => $value) {
			$this->assertEquals(
				$value,
				$e->{$method}(),
				sprintf($msg, $method, $value)
			);
		}
	}

	/**
	 * Provide sets of [exception class name, constructor args, msg] sets
	 * to testExceptionConstructors();
	 *
	 * @return array
	 */
	public function provideTestConstructorsArgs() {
		return array(
			array(
				'StandardJsonApiExceptions', // Exception class to instantiate.
				array( // Args to pass to constructor.
					'title' => null,
					'detail' => null,
					'code' => null,
					'href' => null,
					'id' => null,
				),
				array( // Getter methods to run and expected values.
					'getTitle' => 'JSON API Exception',
					'getDetail' => 'JSON API Exception',
					'getCode' => 400,
					'getHref' => null,
					'getId' => null,
				),
				// optional phpunit assertion failed message here
			),

			array(
				'StandardJsonApiExceptions',
				array(
					'title' => 'a title',
					'detail' => 'some detail',
					'code' => 444,
					'href' => '/right/here/right/now',
					'id' => 12345,
				),
				array(
					'getTitle' => 'a title',
					'getDetail' => 'some detail',
					'getCode' => 444,
					'getHref' => '/right/here/right/now',
					'getId' => 12345,
				),
			),

			array(
				'UnauthorizedJsonApiException',
				array(
					'title' => null,
					'detail' => null,
					'code' => null,
					'href' => null,
					'id' => null,
				),
				array(
					'getTitle' => 'Unauthorized Access',
					'getDetail' => 'Unauthorized Access',
					'getCode' => 401,
				),
			),

			array(
				'ForbiddenByPermissionsException',
				array(
					'title' => null,
					'detail' => null,
					'code' => null,
					'href' => null,
					'id' => null,
				),
				array(
					'getTitle' => 'Unauthorized Access',
					'getDetail' => 'Access to the requested resource is denied by the Permissions on your account.',
					'getCode' => 403,
				),
			),

			array(
				'ValidationFailedJsonApiException',
				array(
					'title' => null,
					'detail' => null,
					'code' => null,
					'href' => null,
					'id' => null,
				),
				array(
					'getTitle' => 'Validation Failed',
					'getDetail' => 'Validation Failed',
					'getCode' => 422,
				),
			),

			array(
				'ModelSaveFailedJsonApiException',
				array(
					'title' => null,
					'detail' => null,
					'code' => null,
					'href' => null,
					'id' => null,
				),
				array(
					'getTitle' => 'Model Save Failed',
					'getDetail' => 'Model Save Failed',
					'getCode' => 400,
				),
			),

			array(
				'InvalidPassedDataJsonApiException',
				array(
					'title' => null,
					'detail' => null,
					'code' => null,
					'href' => null,
					'id' => null,
				),
				array(
					'getTitle' => 'Invalid Data Passed',
					'getDetail' => 'Invalid Data Passed',
					'getCode' => 400,
				),
			),

			array(
				'ModelDeleteFailedJsonApiException',
				array(
					'title' => null,
					'detail' => null,
					'code' => null,
					'href' => null,
					'id' => null,
				),
				array(
					'getTitle' => 'Model Delete Failed',
					'getDetail' => 'Model Delete Failed',
					'getCode' => 502,
				),
			),
		);
	}
}
