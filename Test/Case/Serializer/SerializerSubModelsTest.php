<?php
/**
 * Class to test the serialization methods on sub model records
 */
App::uses('Serializer', 'Serializers.Serializer');
App::uses('Controller', 'Controller');
require_once dirname(__FILE__) . '/serializer_test_classes.php';

/**
 * SerializerSubModelsTest
 */
class SerializerSubModelsTest extends CakeTestCase {

	public function testSubSerializeWithMethodOverride() {
		$data = array(
			'TestMethodSubSerialize' => array(
				'title' => 'Title',
				'body' => 'Body',
				'published' => true,
				'tags' => 'tag1,tag2,tag3',
				'tests' => array(
					'cf08573d-856b-11e4-ba2d-080027506c76',
					'd583c827-856b-11e4-ba2d-080027506c76'
				),
			)
		);
		$serializer = new TestMethodSubSerializeSerializer();
		$expected = array('test_method_sub_serialize' => array(
			'title' => 'Title',
			'body' => 'Body',
			'published' => true,
			'tags' => 'tag1,tag2,tag3',
			'tests' => array(
				'cf08573d-856b-11e4-ba2d-080027506c76',
				'd583c827-856b-11e4-ba2d-080027506c76',
			),
		));
		$this->assertEquals($expected, $serializer->serialize($data));
	}

	public function testSubSerializeWithUpperCaseMethodOverride() {
		$data = array(
			'TestMethodSubSerialize' => array(
				'title' => 'Title',
				'body' => 'Body',
				'published' => true,
				'tags' => 'tag1,tag2,tag3',
				'UpperCaseTest' => array(
					'cf08573d-856b-11e4-ba2d-080027506c76',
					'd583c827-856b-11e4-ba2d-080027506c76'
				),
			)
		);
		$serializer = new TestMethodSubSerializeSerializer();
		$expected = array('test_method_sub_serialize' => array(
			'title' => 'Title',
			'body' => 'Body',
			'published' => true,
			'tags' => 'tag1,tag2,tag3',
			'upper_case_tests' => array(
				'cf08573d-856b-11e4-ba2d-080027506c76',
				'd583c827-856b-11e4-ba2d-080027506c76',
			),
		));
		$this->assertEquals($expected, $serializer->serialize($data));
	}

	/**
	 * test serializing SubModel Records
	 *
	 * @return void
	 */
	public function testSerializeSubModelRecords() {
		$inputData = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => 'Doe',
				'TestSecondLevelUser' => array(
					'first_name' => 'Jane',
					'last_name' => 'Doe',
				),
			),
		);
		$expectedOutput = array(
			'test_user' =>
			array(
				'first_name' => 'John',
				'last_name' => 'Doe',
				'test_second_level_users' => array(
					array(
						'first_name' => 'Jane', 'last_name' => 'Doe',
					)
				),
			),
		);
		$serializer = new TestUserSerializer();
		$this->assertEquals($expectedOutput, $serializer->serialize($inputData));
	}

	public function testSerializeSubModelRecordWithNoData() {
		$inputData = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => 'Doe',
				'TestSecondLevelUser' => array(
				),
			),
		);
		$expectedOutput = array(
			'test_user' =>
			array(
				'first_name' => 'John',
				'last_name' => 'Doe',
				'test_second_level_users' => array(
				),
			),
		);
		$serializer = new TestUserSerializer();
		$this->assertEquals($expectedOutput, $serializer->serialize($inputData));
	}

	public function testSerializeSubModelRecordsWithAttributeMethod() {
		$expectedOutput = array(
			'test_user' =>
			array(
				'first_name' => 'John',
				'last_name' => 'Doe',
				'test_second_level_user_with_methods' => array(
					array(
						'first_name' => 'FIRST',
						'last_name' => 'Doe',
					)
				),
			),
		);
		$inputData = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => 'Doe',
				'TestSecondLevelUserWithMethod' => array(
					'first_name' => 'Jane',
					'last_name' => 'Doe',
				),
			),
		);
		$serializer = new TestUserSerializer();
		$this->assertEquals($expectedOutput, $serializer->serialize($inputData));
	}

	public function testSerializeRelatedRecordsSingleSecondary() {
		$inputData = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => 'Doe',
			),
			'TestSecondLevelUser' => array(
				'first_name' => 'Jane',
				'last_name' => 'Smith',
			),
		);
		$expectedOutput = array(
			'test_user' =>
			array(
				'first_name' => 'John',
				'last_name' => 'Doe',
				'test_second_level_users' => array(
					array(
						'first_name' => 'Jane', 'last_name' => 'Smith',
					),
				),
			),
		);

		$serializer = new TestUserSerializer();
		$this->assertEquals($expectedOutput, $serializer->serialize($inputData));
	}

	public function testSerializeRelatedRecordsMultipleSecondary() {
		$inputData = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => 'Doe',
			),
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
		);
		$expectedOutput = array(
			'test_user' =>
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

		$serializer = new TestUserSerializer();
		$this->assertEquals($expectedOutput, $serializer->serialize($inputData));
	}

	public function testSerializeTwoSubModelRecords() {
		$inputData = array(
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

		$expectedOutput = array(
			'test_user' => array(
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

		$serializer = new TestUserSerializer();
		$this->assertEquals($expectedOutput, $serializer->serialize($inputData));
	}

	public function testSerializeThreeSubModelRecords() {
		$inputData = array(
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
		$expectedOutput = array(
			'test_user' =>
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

		$serializer = new TestUserSerializer();
		$this->assertEquals($expectedOutput, $serializer->serialize($inputData));
	}

	public function testSerializeMultiplePrimaryRecords() {
		$expectedOutput = array(
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
		$inputData = array(
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
		$this->assertEquals($expectedOutput, $serializer->serialize($inputData));
	}

	public function testSerializeMultiplePrimaryRecordsWithSubRecords() {
		$expectedOutput = array(
			'test_users' =>
			array(
				0 => array(
					'first_name' => 'John',
					'last_name' => 'Doe',
					'test_second_level_users' => array(
						0 => array(
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
		$inputData = array(
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
		$this->assertEquals($expectedOutput, $serializer->serialize($inputData));
	}

	public function testSerializeMultiplePrimaryRecordsWithMultipleSubRecords() {
		$expectedOutput = array(
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
		$inputData = array(
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
		$this->assertEquals($expectedOutput, $serializer->serialize($inputData));
	}

	public function testSerializeSinglePrimaryRecordsWithMultipleSubRecords() {
		$expectedOutput = array(
			'test_user' =>
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
		$inputData = array(
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
		$this->assertEquals($expectedOutput, $serializer->serialize($inputData));
	}

	public function testSerializeMultiplePrimaryRecordsAsFromPaginate() {
		$expectedOutput = array(
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
		$inputData = array(
			0 => array(
				'TestUser' => array(
					'first_name' => 'John',
					'last_name' => 'Doe',
				)
			),
			1 => array(
				'TestUser' => array(
					'first_name' => 'Jane',
					'last_name' => 'Smith',
				)
			),
		);
		$serializer = new TestUserSerializer();
		$this->assertEquals($expectedOutput, $serializer->serialize($inputData));
	}

	public function testSerializeMultiplePrimaryRecordsWithSubRecordsAsFromPaginate() {
		$expectedOutput = array(
			'test_users' =>
			array(
				0 => array(
					'first_name' => 'John',
					'last_name' => 'Doe',
					'test_second_level_users' => array(
						0 => array(
							'first_name' => 'Someone',
							'last_name' => 'THings',
						),
					),
				),
				1 => array(
					'first_name' => 'Jane',
					'last_name' => 'Smith',
					'test_second_level_users' => array(
						0 => array(
							'first_name' => 'Random',
							'last_name' => 'Person',
						),
					),
				),
			),
		);
		$inputData = array(
			0 => array(
				'TestUser' => array(
					'first_name' => 'John',
					'last_name' => 'Doe',
					'TestSecondLevelUser' => array(
						'first_name' => 'Someone',
						'last_name' => 'THings',
					),
				)
			),
			1 => array(
				'TestUser' => array(
					'first_name' => 'Jane',
					'last_name' => 'Smith',
					'TestSecondLevelUser' => array(
						'first_name' => 'Random',
						'last_name' => 'Person',
					),
				)
			),
		);
		$serializer = new TestUserSerializer();
		$this->assertEquals($expectedOutput, $serializer->serialize($inputData));
	}

	public function testSerializeMultiplePrimaryRecordsWithMultipleTopLevelModelsAsFromPaginate() {
		$expectedOutput = array(
			'test_users' => array(
				0 => array(
					'first_name' => 'John',
					'last_name' => 'Doe',
					'test_second_level_users' => array(
						0 => array(
							'first_name' => 'Someone',
							'last_name' => 'THings',
						),
					),
				),
				1 => array(
					'first_name' => 'Jane',
					'last_name' => 'Smith',
					'test_second_level_users' => array(
						0 => array(
							'first_name' => 'Random',
							'last_name' => 'Person',
						),
					),
				),
			),
		);
		$inputData = array(
			0 => array(
				'TestUser' => array(
					'first_name' => 'John',
					'last_name' => 'Doe',
				),
				'TestSecondLevelUser' => array(
					'first_name' => 'Someone',
					'last_name' => 'THings',
				),
			),
			1 => array(
				'TestUser' => array(
					'first_name' => 'Jane',
					'last_name' => 'Smith',
				),
				'TestSecondLevelUser' => array(
					'first_name' => 'Random',
					'last_name' => 'Person',
				),
			),
		);
		$serializer = new TestUserSerializer();
		$this->assertEquals($expectedOutput, $serializer->serialize($inputData));
	}

	public function testSerializeMultiplePrimaryRecordsWithMultipleRecords() {
		$expectedOutput = array(
			'test_user' => array(
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
		$inputData = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => 'Doe',
			),
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
		);
		$serializer = new TestUserSerializer();
		$this->assertEquals($expectedOutput, $serializer->serialize($inputData));
	}

	public function testMissingRequiredAttributeOnSecondaryModelRecord() {
		$inputData = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => 'Doe',
			),
			'TestSecondLevelUser' => array(
				0 => array(
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
		);
		$serializer = new TestUserSerializer();
		$this->setExpectedException(
			'SerializerMissingRequiredException',
			"The following keys were missing from TestSecondLevelUser: first_name"
		);
		$serializer->serialize($inputData);
	}

	public function testMissingRequiredAttributeOnSubModelRecord() {
		$inputData = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => 'Doe',
				'TestSecondLevelUser' => array(
					0 => array(
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
		$this->setExpectedException(
			'SerializerMissingRequiredException',
			"The following keys were missing from TestSecondLevelUser: first_name"
		);
		$serializer->serialize($inputData);
	}

	public function testMissingRequiredAttributeOnSecondaryModelRecordWithASingleRecord() {
		$inputData = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => 'Doe',
			),
			'TestSecondLevelDifferentClass' => array(
				'name' => 'Smith',
			),
		);
		$serializer = new TestUserSerializer();
		$this->setExpectedException(
			'SerializerMissingRequiredException',
			"The following keys were missing from TestSecondLevelDifferentClass: id"
		);
		$serializer->serialize($inputData);
	}

	public function testMissingRequiredAttributeOnSubModelRecordWithASingleRecord() {
		$inputData = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => 'Doe',
				'TestSecondLevelDifferentClass' => array(
					'name' => 'Smith',
				),
			),
		);
		$serializer = new TestUserSerializer();
		$this->setExpectedException(
			'SerializerMissingRequiredException',
			"The following keys were missing from TestSecondLevelDifferentClass: id"
		);
		$serializer->serialize($inputData);
	}

	public function testMissingRequiredAttributeOnSecondaryModelRecordWithDifferentFieldNames() {
		$inputData = array(
			'TestPrimary' => array(
				'id' => '1',
				'name' => 'Doe',
			),
			'TestSubSecondary' => array(
				0 => array(
					'test_field' => 'Smith',
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
		);
		$serializer = new TestPrimarySerializer();
		$this->setExpectedException(
			'SerializerMissingRequiredException',
			"The following keys were missing from TestSubSecondary: test_field"
		);
		$serializer->serialize($inputData);
	}

	public function testMissingRequiredAttributeOnSubModelRecordWithDifferentFieldNames() {
		$inputData = array(
			'TestPrimary' => array(
				'id' => '1',
				'name' => 'Doe',
				'TestSubSecondary' => array(
					0 => array(
						'test_field' => 'Smith',
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
		$serializer = new TestPrimarySerializer();
		$this->setExpectedException(
			'SerializerMissingRequiredException',
			"The following keys were missing from TestSubSecondary: test_field"
		);
		$serializer->serialize($inputData);
	}

}
