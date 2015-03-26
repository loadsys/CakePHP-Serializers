<?php
/**
 * Class to test the serialization methods on data that has only a top level
 * model
 */
App::uses('Serializer', 'Serializers.Serializer');
App::uses('Controller', 'Controller');
require_once dirname(__FILE__) . '/serializer_test_classes.php';

/**
 * SerializerTopLevelTest
 */
class SerializerTopLevelTest extends CakeTestCase {

	/**
	 * test Serializing the TestUserSerializer Model
	 *
	 * @return void
	 */
	public function testSerializerUsesAttributesInAttributesArray() {
		$data = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => 'Doe'
			)
		);
		$expected = array(
			'test_user' => array(
				'first_name' => 'John',
				'last_name' => 'Doe'
			)
		);

		$TestUserSerializer = new TestUserSerializer();
		$this->assertEquals(
			$expected,
			$TestUserSerializer->serialize($data),
			'The output of serialize did not match the expected output'
		);
	}

	/**
	 * test Serializing when no input data to the Serialize method
	 *
	 * @return void
	 */
	public function testSerializerUsesNoDataPassedToTheSerializerArray() {
		$data = array(
		);
		$expected = array(
			'test_users' => array(),
		);

		$TestUserSerializer = new TestUserSerializer();
		$this->assertEquals(
			$expected,
			$TestUserSerializer->serialize($data),
			'The output of serialize did not match the expected output'
		);
	}

	/**
	 * test Serializing when an empty array input data to the Serialize method
	 *
	 * @return void
	 */
	public function testSerializerUsesEmptyDataPassedToTheSerializerArray() {
		$data = array(
			'TestUser' => array()
		);
		$expected = array('test_user' => array(
		));

		$TestUserSerializer = new TestUserSerializer();
		$this->assertEquals(
			$expected,
			$TestUserSerializer->serialize($data),
			'The output of serialize did not match the expected output'
		);
	}

	/**
	 * test the afterSerialize callback method
	 *
	 * @return void
	 */
	public function testSerializerAfterSerializeCallback() {
		$data = array(
			array("TestCallback" => array())
		);
		$expected = "after serialize";

		$TestCallbackSerializer = new TestCallbackSerializer();
		$this->assertEquals(
			$expected,
			$TestCallbackSerializer->serialize($data),
			'The output of serialize did not match the expected output, the afterSerialize callback was not called'
		);
	}

}
