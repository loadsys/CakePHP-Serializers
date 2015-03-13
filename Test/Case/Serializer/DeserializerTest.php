<?php
/**
 * Class to test the deserialization methods
 */
App::uses('Serializer', 'Serializers.Serializer');
App::uses('Controller', 'Controller');
App::uses('Lib/Error', 'Serializers.SerializerExceptions');
require_once( dirname(__FILE__) . '/serializer_test_classes.php');

class DeserializerTest extends CakeTestCase {
	public function testRootKeyGeneration() {
		$serializer = new TestRootKeySerializer();
		$this->assertEquals('TestRootKey', $serializer->rootKey);
	}

	public function testDeserializerUsesAttributesInAttributesArray() {
		$expected = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => 'Doe',
			),
		);
		$serializer = new TestUserSerializer();
		$data = array('test_users' => array(
			'first_name' => 'John', 'last_name' => 'Doe'
		));
		$this->assertEquals($expected, $serializer->deserialize($data));
	}

	public function testDeserializerSingular() {
		$expected = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => 'Doe',
			),
		);
		$serializer = new TestUserSerializer();
		$data = array('test_user' => array(
			'first_name' => 'John', 'last_name' => 'Doe'
		));
		$this->assertEquals($expected, $serializer->deserialize($data));
	}

	public function testDeserializerSingularWithSecondTopLevelModel() {
		$expected = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => 'Doe',
			),
		);
		$serializer = new TestUserSerializer();
		$data = array(
			'test_user' => array(
				'first_name' => 'John', 'last_name' => 'Doe'
			),
			'test_second_level_user' => array(
				'first_name' => 'Jane', 'last_name' => 'Smith'
			)
		);
		$this->assertEquals($expected, $serializer->deserialize($data));
	}

	public function testDeserializerUsesNoDataPassedToTheSerializerArray() {
		$data = array(
		);
		$serializer = new TestUserSerializer();
		$expected = array();
		$this->assertEquals($expected, $serializer->deserialize($data));
	}

	public function testDeserializerUsesEmptyDataPassedToTheSerializerArray() {
		$data = array(
		);
		$serializer = new TestUserSerializer();
		$expected = array();
		$this->assertEquals($expected, $serializer->deserialize($data));
	}

	public function testDeserializerAfterDeserializeCallback() {
		$serializer = new TestCallbackSerializer();
		$data = array('test_callbacks' => array(
			'first_name' => 'John', 'last_name' => 'Doe'
		));
		$expected = "after deserialize";
		$this->assertEquals($expected, $serializer->deserialize($data));
	}

	public function testDeserializerAfterDeserializeCallbackWithSingularCase() {
		$serializer = new TestCallbackSerializer();
		$data = array('test_callback' => array(
			'first_name' => 'John', 'last_name' => 'Doe'
		));
		$expected = "after deserialize";
		$this->assertEquals($expected, $serializer->deserialize($data));
	}

	public function testDeserializeNoData() {
		$data = null;
		$expected = null;

		$serializer = new TestRootKeySerializer();
		$this->assertEquals($expected, $serializer->deserialize($data));
	}

	public function testDeserializeDataWithMethod() {
		$expected = array(
			'TestOptional' => array(
				'title' => 'Title',
				'body' => 'BODY',
				'summary' => 'SUMMARY',
				'published' => true,
			),
		);
		$data = array('test_optionals' => array(
			'title' => 'Title',
			'body' => 'Body',
			'summary' => 'Summary',
			'published' => true
		));

		$serializer = new TestOptionalSerializer();
		$this->assertEquals($expected, $serializer->deserialize($data));
	}

	public function testDeserializeSingularDataWithMethod() {
		$expected = array(
			'TestOptional' => array(
				'title' => 'Title',
				'body' => 'BODY',
				'summary' => 'SUMMARY',
				'published' => true,
			),
		);
		$data = array('test_optional' => array(
			'title' => 'Title',
			'body' => 'Body',
			'summary' => 'Summary',
			'published' => true
		));

		$serializer = new TestOptionalSerializer();
		$this->assertEquals($expected, $serializer->deserialize($data));
	}

	public function testDeserializeIgnoreAttribute() {
		$expected = array(
			'TestIgnore' => array(
				'title' => 'Title',
				'body' => 'Body',
			),
		);
		$data = array('test_ignores' => array(
			'title' => 'Title',
			'body' => 'Body',
			'created' => '2014-07-07',
		));

		$serializer = new TestIgnoreSerializer();
		$this->assertEquals($expected, $serializer->deserialize($data));
	}

	public function testDeserializeSubModelRecords() {
		$expected = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => 'Doe',
				'TestSecondLevelUser' => array(
					'first_name' => 'Jane',
					'last_name' => 'Doe',
				),
			),
		);
		$serializer = new TestUserSerializer();
		$data = array(
			'test_user' =>
			array(
				'first_name' => 'John',
				'last_name' => 'Doe',
				'test_second_level_user' => array(
					'first_name' => 'Jane', 'last_name' => 'Doe',
				),
			),
		);
		$this->assertEquals($expected, $serializer->deserialize($data));
	}

	public function testDeserializeSubModelRecordsWithAttributeMethod() {
		$inputData = array(
			'test_users' =>
			array(
				'first_name' => 'John',
				'last_name' => 'Doe',
				'test_second_level_user_with_methods' => array(
					'first_name' => 'Jane',
					'last_name' => 'Doe',
				),
			),
		);
		$expectedOutput = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => 'Doe',
				'TestSecondLevelUserWithMethod' => array(
					'first_name' => 'FIRST',
					'last_name' => 'Doe',
				),
			),
		);
		$serializer = new TestUserSerializer();
		$this->assertEquals($expectedOutput, $serializer->deserialize($inputData));
	}

	public function testDeserializeSingularSubModelRecords() {
		$inputData = array(
			'test_user' =>
			array(
				'first_name' => 'John',
				'last_name' => 'Doe',
				'test_second_level_user_with_method' => array(
					'first_name' => 'Jane',
					'last_name' => 'Doe',
				),
			),
		);
		$expectedOutput = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => 'Doe',
				'TestSecondLevelUserWithMethod' => array(
					'first_name' => 'FIRST',
					'last_name' => 'Doe',
				),
			),
		);
		$serializer = new TestUserSerializer();
		$this->assertEquals($expectedOutput, $serializer->deserialize($inputData));
	}

	public function testDeserializeTwoSubModelRecords() {
		$expected = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => 'Doe',
				'TestSecondLevelUser' => array(
					0 => array(
						'first_name' => 'Jane',
						'last_name' => 'Smith',
					),
					1 => array(
						'first_name' => 'Jane',
						'last_name' => 'Text',
					),
				),
			),
		);
		$serializer = new TestUserSerializer();
		$data = array(
			'test_users' =>
			array(
				'first_name' => 'John',
				'last_name' => 'Doe',
				'test_second_level_users' => array(
					0 => array(
						'first_name' => 'Jane', 'last_name' => 'Smith',
					),
					1 => array(
						'first_name' => 'Jane', 'last_name' => 'Text',
					),
				),
			),
		);
		$this->assertEquals($expected, $serializer->deserialize($data));
	}

	public function testDeserializeThreeSubModelRecords() {
		$expected = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => 'Doe',
				'TestSecondLevelUser' => array(
					0 => array(
						'first_name' => 'Jane',
						'last_name' => 'Smith',
					),
					1 => array(
						'first_name' => 'Jane',
						'last_name' => 'Text',
					),
					2 => array(
						'first_name' => 'Jane',
						'last_name' => 'Ipsum',
					),
				),
			),
		);
		$serializer = new TestUserSerializer();
		$data = array(
			'test_users' =>
			array(
				'first_name' => 'John',
				'last_name' => 'Doe',
				'test_second_level_users' => array(
					0 => array(
						'first_name' => 'Jane',
						'last_name' => 'Smith',
					),
					1 => array(
						'first_name' => 'Jane',
						'last_name' => 'Text',
					),
					2 => array(
						'first_name' => 'Jane',
						'last_name' => 'Ipsum',
					),
				),
			),
		);
		$this->assertEquals($expected, $serializer->deserialize($data));
	}

	public function testDeserializeMultiplePrimaryRecords() {
		$inputData = array(
			'test_users' =>
			array(
				0 => array(
					'first_name' => 'John',
					'last_name' => 'Doe',
				),
				1 => array(
					'first_name' => 'Jane',
					'last_name' => 'Smith',
				),
			),
		);
		$expectedOutput = array(
			'TestUser' => array(
				0 => array(
					'first_name' => 'John',
					'last_name' => 'Doe',
				),
				1 => array(
					'first_name' => 'Jane',
					'last_name' => 'Smith',
				),
			),
		);
		$serializer = new TestUserSerializer();
		$this->assertEquals($expectedOutput, $serializer->deserialize($inputData));
	}

	public function testDeserializeMultiplePrimaryRecordsWithSubRecords() {
		$inputData = array(
			'test_users' =>
			array(
				0 => array(
					'first_name' => 'John',
					'last_name' => 'Doe',
					'test_second_level_users' => array(
						'first_name' => 'Jane',
						'last_name' => 'Ipsum',
					),
				),
				1 => array(
					'first_name' => 'Jane',
					'last_name' => 'Smith',
				),
			),
		);
		$expectedOutput = array(
			'TestUser' => array(
				0 => array(
					'first_name' => 'John',
					'last_name' => 'Doe',
					'TestSecondLevelUser' => array(
						'first_name' => 'Jane',
						'last_name' => 'Ipsum',
					),
				),
				1 => array(
					'first_name' => 'Jane',
					'last_name' => 'Smith',
				),
			),
		);
		$serializer = new TestUserSerializer();
		$this->assertEquals($expectedOutput, $serializer->deserialize($inputData));
	}

	public function testDeserializeMultiplePrimaryRecordsWithMultipleSubRecords() {
		$inputData = array(
			'test_users' =>
			array(
				0 => array(
					'first_name' => 'John',
					'last_name' => 'Doe',
					'test_second_level_users' => array(
						0 => array(
							'first_name' => 'Jane',
							'last_name' => 'Smith',
						),
						1 => array(
							'first_name' => 'Jane',
							'last_name' => 'Text',
						),
						2 => array(
							'first_name' => 'Jane',
							'last_name' => 'Ipsum',
						),
					),
				),
				1 => array(
					'first_name' => 'Jane',
					'last_name' => 'Smith',
				),
			),
		);
		$expectedOutput = array(
			'TestUser' => array(
				0 => array(
					'first_name' => 'John',
					'last_name' => 'Doe',
					'TestSecondLevelUser' => array(
						0 => array(
							'first_name' => 'Jane',
							'last_name' => 'Smith',
						),
						1 => array(
							'first_name' => 'Jane',
							'last_name' => 'Text',
						),
						2 => array(
							'first_name' => 'Jane',
							'last_name' => 'Ipsum',
						),
					),
				),
				1 => array(
					'first_name' => 'Jane',
					'last_name' => 'Smith',
				),
			),
		);
		$serializer = new TestUserSerializer();
		$this->assertEquals($expectedOutput, $serializer->deserialize($inputData));
	}

}
