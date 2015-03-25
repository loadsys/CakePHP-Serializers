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
	 * @param  string $class The Exception class name to instantiate.
	 * @param	array	$args An array of args to pass to the constructor method.
	 * @param  array $expected The expected properties of the Exception class
	 * @param  string $msg Optional PHPUnit error message when the assertion fails.
	 * @return void
	 * @dataProvider	provideTestConstructorsArgs
	 */
	public function testExceptionConstructors($class, $args, $expected, $msg = 'The method ::%1$s() is expected to return the value `%2$s`.') {
		extract($args);
		$e = new $class($title, $detail, $code, $href, $id);
		foreach ($expected as $method => $value) {
			if (is_array($value)) {
				$this->assertEquals(
					$value,
					$e->{$method}(),
					sprintf($msg, $method, implode($value, ", "))
				);
			} else {
				$this->assertEquals(
					$value,
					$e->{$method}(),
					sprintf($msg, $method, $value)
				);
			}
		}
	}

	/**
	 * Provide sets of [exception class name, constructor args, expected, msg] sets
	 * to testExceptionConstructors();
	 *
	 * @return array data inputs to testExceptionConstructors
	 */
	public function provideTestConstructorsArgs() {
		return array(
			array(
				'StandardJsonApiExceptions', // Exception class to instantiate.
				array( // Args to pass to constructor.
					'title' => 'JSON API Exception',
					'detail' => 'JSON API Exception',
					'code' => 400,
					'href' => '/right/here/right/now',
					'id' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
				array( // Getter methods to run and expected values.
					'getTitle' => 'JSON API Exception',
					'getDetail' => 'JSON API Exception',
					'getCode' => 400,
					'getHref' => '/right/here/right/now',
					'getId' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
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
					'id' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
				array(
					'getTitle' => 'a title',
					'getDetail' => 'some detail',
					'getCode' => 444,
					'getHref' => '/right/here/right/now',
					'getId' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
			),

			array(
				'NotFoundJsonApiException',
				array(
					'title' => 'Resource Not Found',
					'detail' => 'Resource Not Found',
					'code' => 404,
					'href' => '/right/here/right/now',
					'id' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
				array(
					'getTitle' => 'Resource Not Found',
					'getDetail' => 'Resource Not Found',
					'getCode' => 404,
					'getHref' => '/right/here/right/now',
					'getId' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
			),

			array(
				'UnauthorizedJsonApiException',
				array(
					'title' => 'Unauthorized Access',
					'detail' => 'Unauthorized Access',
					'code' => 401,
					'href' => '/right/here/right/now',
					'id' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
				array(
					'getTitle' => 'Unauthorized Access',
					'getDetail' => 'Unauthorized Access',
					'getCode' => 401,
					'getHref' => '/right/here/right/now',
					'getId' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
			),

			array(
				'ForbiddenByPermissionsException',
				array(
					'title' => 'Unauthorized Access',
					'detail' => 'Access to the requested resource is denied by the Permissions on your account.',
					'code' => 403,
					'href' => '/right/here/right/now',
					'id' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
				array(
					'getTitle' => 'Unauthorized Access',
					'getDetail' => 'Access to the requested resource is denied by the Permissions on your account.',
					'getCode' => 403,
					'getHref' => '/right/here/right/now',
					'getId' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
			),

			array(
				'ValidationFailedJsonApiException',
				array(
					'title' => 'Validation Failed',
					'detail' => array(
						'name' => 'invalid name',
						'boolean' => 'invalid boolean field',
					),
					'code' => 422,
					'href' => '/right/here/right/now',
					'id' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
				array(
					'getTitle' => 'Validation Failed',
					'getDetail' => array(
						'name' => 'invalid name',
						'boolean' => 'invalid boolean field',
					),
					'getCode' => 422,
					'getHref' => '/right/here/right/now',
					'getId' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
			),

			array(
				'ModelSaveFailedJsonApiException',
				array(
					'title' => 'Model Save Failed',
					'detail' => 'Model Save Failed',
					'code' => 400,
					'href' => '/right/here/right/now',
					'id' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
				array(
					'getTitle' => 'Model Save Failed',
					'getDetail' => 'Model Save Failed',
					'getCode' => 400,
					'getHref' => '/right/here/right/now',
					'getId' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
			),

			array(
				'InvalidPassedDataJsonApiException',
				array(
					'title' => 'Invalid Data Passed',
					'detail' => 'Invalid Data Passed',
					'code' => 400,
					'href' => '/right/here/right/now',
					'id' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
				array(
					'getTitle' => 'Invalid Data Passed',
					'getDetail' => 'Invalid Data Passed',
					'getCode' => 400,
					'getHref' => '/right/here/right/now',
					'getId' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
			),

			array(
				'ModelDeleteFailedJsonApiException',
				array(
					'title' => 'Model Delete Failed',
					'detail' => 'Model Delete Failed',
					'code' => 502,
					'href' => '/right/here/right/now',
					'id' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
				array(
					'getTitle' => 'Model Delete Failed',
					'getDetail' => 'Model Delete Failed',
					'getCode' => 502,
					'getHref' => '/right/here/right/now',
					'getId' => 'a14d2c0c-c9d1-11e4-ba2d-080027506c76',
				),
			),
		);
	}

	/**
	 * Confirm that all Exception constructors set default args into the
	 * correct properties. As a side-effect, also tests the getter methods
	 * for all properties.
	 *
	 * @param  string $class The Exception class name to instantiate.
	 * @param  array $expected The expected properties of the Exception class
	 * @param  string $msg Optional PHPUnit error message when the assertion fails.
	 * @return void
	 * @dataProvider	provideTestConstructorsDefaultValues
	 */
	public function testExceptionConstructorsDefaultValues($class, $expected, $msg = 'The method ::%1$s() is expected to return the value `%2$s`.') {
		$e = new $class();
		foreach ($expected as $method => $value) {
			if (is_array($value)) {
				$this->assertEquals(
					$value,
					$e->{$method}(),
					sprintf($msg, $method, implode($value, ", "))
				);
			} else {
				$this->assertEquals(
					$value,
					$e->{$method}(),
					sprintf($msg, $method, $value)
				);
			}
		}
	}

	/**
	 * Provide sets of [exception class name, expected, msg] sets
	 * to testExceptionConstructorsDefaultValues();
	 *
	 * @return array data inputs to testExceptionConstructorsDefaultValues
	 */
	public function provideTestConstructorsDefaultValues() {
		return array(
			array(
				'StandardJsonApiExceptions', // Exception class to instantiate.
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
				'NotFoundJsonApiException',
				array(
					'getTitle' => 'Resource Not Found',
					'getDetail' => 'Resource Not Found',
					'getCode' => 404,
					'getHref' => null,
					'getId' => null,
				),
			),

			array(
				'UnauthorizedJsonApiException',
				array(
					'getTitle' => 'Unauthorized Access',
					'getDetail' => 'Unauthorized Access',
					'getCode' => 401,
					'getHref' => null,
					'getId' => null,
				),
			),

			array(
				'ForbiddenByPermissionsException',
				array(
					'getTitle' => 'Unauthorized Access',
					'getDetail' => 'Access to the requested resource is denied by the Permissions on your account.',
					'getCode' => 403,
					'getHref' => null,
					'getId' => null,
				),
			),

			array(
				'ValidationFailedJsonApiException',
				array(
					'getTitle' => 'Validation Failed',
					'getDetail' => array(),
					'getCode' => 422,
					'getHref' => null,
					'getId' => null,
				),
			),

			array(
				'ModelSaveFailedJsonApiException',
				array(
					'getTitle' => 'Model Save Failed',
					'getDetail' => 'Model Save Failed',
					'getCode' => 400,
					'getHref' => null,
					'getId' => null,
				),
			),

			array(
				'InvalidPassedDataJsonApiException',
				array(
					'getTitle' => 'Invalid Data Passed',
					'getDetail' => 'Invalid Data Passed',
					'getCode' => 400,
					'getHref' => null,
					'getId' => null,
				),
			),

			array(
				'ModelDeleteFailedJsonApiException',
				array(
					'getTitle' => 'Model Delete Failed',
					'getDetail' => 'Model Delete Failed',
					'getCode' => 502,
					'getHref' => null,
					'getId' => null,
				),
			),
		);
	}

}
