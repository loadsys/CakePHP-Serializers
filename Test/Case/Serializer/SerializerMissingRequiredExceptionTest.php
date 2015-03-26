<?php
/**
 * Class to test the SerializerMissingRequiredException is working correctly
 */
App::uses('Serializer', 'Serializers.Serializer');
App::uses('Controller', 'Controller');
require_once dirname(__FILE__) . '/serializer_test_classes.php';

/**
 * SerializerMissingRequiredExceptionTest
 */
class SerializerMissingRequiredExceptionTest extends CakeTestCase {

	/**
	 * test the SerializerMissingRequiredException
	 *
	 * @return void
	 */
	public function testMissingRequiredAttribute() {
		$data = array(
			'TestUser' => array(
				'first_name' => 'John'
			)
		);
		$TestUserSerializer = new TestUserSerializer();
		$this->setExpectedException(
			"SerializerMissingRequiredException",
			"The following keys were missing from TestUser: last_name"
		);
		$output = $TestUserSerializer->serialize($data);
	}

	/**
	 * test that a null required attribute does not throw the
	 * SerializerMissingRequiredException and serializes correctly
	 *
	 * @return void
	 */
	public function testNullRequiredAttribute() {
		$userData = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => null,
			)
		);
		$expectedResult = array(
			'test_user' => array(
				'first_name' => 'John',
				'last_name' => null,
			),
		);
		$TestUserSerializer = new TestUserSerializer();
		$result = $TestUserSerializer->serialize($userData);
		$this->assertEquals(
			$expectedResult,
			$result,
			"The result from serialize did not match the expected result"
		);
	}

	/**
	 * test that a empty string required attribute does not throw the
	 * SerializerMissingRequiredException and serializes correctly
	 *
	 * @return void
	 */
	public function testEmptyStringRequiredAttribute() {
		$userData = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => '',
			)
		);
		$expectedResult = array(
			'test_user' => array(
				'first_name' => 'John',
				'last_name' => '',
			),
		);
		$TestUserSerializer = new TestUserSerializer();
		$result = $TestUserSerializer->serialize($userData);
		$this->assertEquals(
			$expectedResult,
			$result,
			"The result from serialize did not match the expected result"
		);
	}

	/**
	 * test that a zero int required attribute does not throw the
	 * SerializerMissingRequiredException and serializes correctly
	 *
	 * @return void
	 */
	public function testZeroIntRequiredAttribute() {
		$userData = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => 0,
			)
		);
		$expectedResult = array(
			'test_user' => array(
				'first_name' => 'John',
				'last_name' => 0,
			),
		);
		$TestUserSerializer = new TestUserSerializer();
		$result = $TestUserSerializer->serialize($userData);
		$this->assertEquals(
			$expectedResult,
			$result,
			"The result from serialize did not match the expected result"
		);
	}

	/**
	 * test that a false required attribute does not throw the
	 * SerializerMissingRequiredException and serializes correctly
	 *
	 * @return void
	 */
	public function testFalseRequiredAttribute() {
		$userData = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => false,
			)
		);
		$expectedResult = array(
			'test_user' => array(
				'first_name' => 'John',
				'last_name' => false,
			),
		);
		$TestUserSerializer = new TestUserSerializer();
		$result = $TestUserSerializer->serialize($userData);
		$this->assertEquals(
			$expectedResult,
			$result,
			"The result from serialize did not match the expected result"
		);
	}

}
