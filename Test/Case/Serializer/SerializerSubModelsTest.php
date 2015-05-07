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

	/**
	 * test sub serialization when a method should be called and override the
	 * call to the sub serializer
	 *
	 * @return void
	 */
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

		$TestMethodSubSerializeSerializer = new TestMethodSubSerializeSerializer();
		$this->assertEquals(
			$expected,
			$TestMethodSubSerializeSerializer->serialize($data),
			'The output of serialize did not match the expected return, the method for a sub property was not called'
		);
	}

	/**
	 * verify SubSerialization methods work correctly with UpperCase attributes, ie
	 * a sub-model
	 *
	 * @return void
	 */
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

		$TestMethodSubSerializeSerializer = new TestMethodSubSerializeSerializer();
		$this->assertEquals(
			$expected,
			$TestMethodSubSerializeSerializer->serialize($data),
			'The output of serialize did not match the expected return, the method for a sub object was not called'
		);
	}

	/**
	 * test serializing SubModel Records in the case of the sub model having no data,
	 * the model name should be converted to be plural and an array of arrays
	 *
	 * @return void
	 */
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

		$TestUserSerializer = new TestUserSerializer();
		$this->assertEquals(
			$expectedOutput,
			$TestUserSerializer->serialize($inputData),
			'The output of serialize did not match the expected return, the sub object did not serialize properly'
		);
	}

	/**
	 * test serializing SubModel Records in the basic case, the model name should
	 * be converted to be plural and an array of arrays
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

		$TestUserSerializer = new TestUserSerializer();
		$this->assertEquals(
			$expectedOutput,
			$TestUserSerializer->serialize($inputData),
			'The output of serialize did not match the expected return, the sub object did not serialize properly'
		);
	}

	/**
	 * test serializing sub model records with an attribute method
	 *
	 * @return void
	 */
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

		$TestUserSerializer = new TestUserSerializer();
		$this->assertEquals(
			$expectedOutput,
			$TestUserSerializer->serialize($inputData),
			'The output of serialize did not match the expected return, the sub object did not serialize properly, the method on the sub object for the `first_name` property was not called'
		);
	}

	/**
	 * test serializing a related model to verify it is moved to it's proper location
	 * and keys are converted correctly
	 *
	 * @return void
	 */
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

		$TestUserSerializer = new TestUserSerializer();
		$this->assertEquals(
			$expectedOutput,
			$TestUserSerializer->serialize($inputData),
			'The output of serialize did not match the expected return, the related object did not serialize properly, the related model was not properly moved to its final location in the return array.'
		);
	}

	/**
	 * test serializing a related model with multiple records
	 *
	 * @return void
	 */
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

		$TestUserSerializer = new TestUserSerializer();
		$this->assertEquals(
			$expectedOutput,
			$TestUserSerializer->serialize($inputData),
			'The output of serialize did not match the expected return, the related object did not serialize properly.'
		);
	}

	/**
	 * test serializing a sub model with two records
	 *
	 * @return void
	 */
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

		$TestUserSerializer = new TestUserSerializer();
		$this->assertEquals(
			$expectedOutput,
			$TestUserSerializer->serialize($inputData),
			'The output of serialize did not match the expected return, the sub object did not serialize properly.'
		);
	}

	/**
	 * test serializing a sub model with three records
	 *
	 * @return void
	 */
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

		$TestUserSerializer = new TestUserSerializer();
		$this->assertEquals(
			$expectedOutput,
			$TestUserSerializer->serialize($inputData),
			'The output of serialize did not match the expected return, the sub object did not serialize properly.'
		);
	}

	/**
	 * test serializing a model with multiple primary records, and at least one
	 * instance of a sub model
	 *
	 * @return void
	 */
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

		$TestUserSerializer = new TestUserSerializer();
		$this->assertEquals(
			$expectedOutput,
			$TestUserSerializer->serialize($inputData),
			'The output of serialize did not match the expected return, the sub object did not serialize properly.'
		);
	}

	/**
	 * test serializing multiple primary records with multiple sub model records
	 *
	 * @return void
	 */
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

		$TestUserSerializer = new TestUserSerializer();
		$this->assertEquals(
			$expectedOutput,
			$TestUserSerializer->serialize($inputData),
			"The output of serialize did not match the the expected output"
		);
	}

	/**
	 * test serializing a single primary record with multiple sub model records
	 *
	 * @return void
	 */
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

		$TestUserSerializer = new TestUserSerializer();
		$this->assertEquals(
			$expectedOutput,
			$TestUserSerializer->serialize($inputData),
			"The output of serialize did not match the the expected output"
		);
	}

	/**
	 * test serializing multiple primary records with a single secondary record
	 * that are from a CakePHP pagination call
	 *
	 * @return void
	 */
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

		$TestUserSerializer = new TestUserSerializer();
		$this->assertEquals(
			$expectedOutput,
			$TestUserSerializer->serialize($inputData),
			"The output of serialize did not match the the expected output"
		);
	}

	/**
	 * test serializing multiple primary records with multiple secondary records
	 * that are from a CakePHP pagination call
	 *
	 * @return void
	 */
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

		$TestUserSerializer = new TestUserSerializer();
		$this->assertEquals(
			$expectedOutput,
			$TestUserSerializer->serialize($inputData),
			"The output of serialize did not match the the expected output"
		);
	}

	/**
	 * test serializing multiple primary records with multiple secondary records
	 * attached to each primary model that are from a CakePHP pagination call
	 *
	 * @return void
	 */
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

		$TestUserSerializer = new TestUserSerializer();
		$this->assertEquals(
			$expectedOutput,
			$TestUserSerializer->serialize($inputData),
			"The output of serialize did not match the the expected output"
		);
	}

	/**
	 * test serializing secondary model records with a missing required attribute
	 *
	 * @return void
	 */
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

		$TestUserSerializer = new TestUserSerializer();
		$this->setExpectedException(
			'SerializerMissingRequiredException',
			"The following keys were missing from TestSecondLevelUser: first_name"
		);
		$output = $TestUserSerializer->serialize($inputData);
	}

	/**
	 * test serializing sub model records with a missing required attribute
	 *
	 * @return void
	 */
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

		$TestUserSerializer = new TestUserSerializer();
		$this->setExpectedException(
			'SerializerMissingRequiredException',
			"The following keys were missing from TestSecondLevelUser: first_name"
		);
		$output = $TestUserSerializer->serialize($inputData);
	}

	/**
	 * test a missing required attribute on a secondary model record with a single
	 * record
	 *
	 * @return void
	 */
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

		$TestUserSerializer = new TestUserSerializer();
		$this->setExpectedException(
			'SerializerMissingRequiredException',
			"The following keys were missing from TestSecondLevelDifferentClass: id"
		);
		$output = $TestUserSerializer->serialize($inputData);
	}

	/**
	 * test a missing required attribute on a sub model record with a single
	 * record
	 *
	 * @return void
	 */
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

		$TestUserSerializer = new TestUserSerializer();
		$this->setExpectedException(
			'SerializerMissingRequiredException',
			"The following keys were missing from TestSecondLevelDifferentClass: id"
		);
		$output = $TestUserSerializer->serialize($inputData);
	}

	/**
	 * test a missing required attribute on a secondary model records with different
	 * field names, verifying the required attribute is set properly for a single secondary
	 * models
	 *
	 * @return void
	 */
	public function testMissingRequiredAttributeOnSingleSecondaryModelRecordWithDifferentFieldNames() {
		$inputData = array(
			'TestPrimary' => array(
				'id' => '1',
				'name' => 'Doe',
			),
			'TestSubSecondary' => array(
				'first_name' => 'Jane',
				'last_name' => 'Text',
			),
		);

		$TestPrimarySerializer = new TestPrimarySerializer();
		$this->setExpectedException(
			'SerializerMissingRequiredException',
			"The following keys were missing from TestSubSecondary: test_field"
		);
		$output = $TestPrimarySerializer->serialize($inputData);
	}

	/**
	 * test a missing required attribute on multiple sub model records with different
	 * field names, verifying the required attribute is set properly for a single sub
	 * models
	 *
	 * @return void
	 */
	public function testMissingRequiredAttributeOnSingleSubModelRecordWithDifferentFieldNames() {
		$inputData = array(
			'TestPrimary' => array(
				'id' => '1',
				'name' => 'Doe',
				'TestSubSecondary' => array(
					'first_name' => 'Jane',
					'last_name' => 'Text',
				),
			),
		);

		$TestPrimarySerializer = new TestPrimarySerializer();
		$this->setExpectedException(
			'SerializerMissingRequiredException',
			"The following keys were missing from TestSubSecondary: test_field"
		);
		$output = $TestPrimarySerializer->serialize($inputData);
	}

	/**
	 * test a missing required attribute on multiple secondary model records with different
	 * field names, verifying the required attribute is set properly for secondary
	 * models, as well as verifying that the exception is not only thrown on the first
	 * record
	 *
	 * @return void
	 */
	public function testMissingRequiredAttributeOnSecondaryModelRecordsWithDifferentFieldNames() {
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

		$TestPrimarySerializer = new TestPrimarySerializer();
		$this->setExpectedException(
			'SerializerMissingRequiredException',
			"The following keys were missing from TestSubSecondary: test_field"
		);
		$output = $TestPrimarySerializer->serialize($inputData);
	}

	/**
	 * test a missing required attribute on multiple sub model records with different
	 * field names , verifying the required attribute is set properly for sub
	 * models, as well as verifying that the exception is not only thrown on the first
	 * record
	 *
	 * @return void
	 */
	public function testMissingRequiredAttributeOnSubModelRecordsWithDifferentFieldNames() {
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

		$TestPrimarySerializer = new TestPrimarySerializer();
		$this->setExpectedException(
			'SerializerMissingRequiredException',
			"The following keys were missing from TestSubSecondary: test_field"
		);
		$output = $TestPrimarySerializer->serialize($inputData);
	}

	/**
	 * test throwing a SerializerIgnoreException on a sub model record
	 *
	 * @return void
	 */
	public function testThrowingSerializerIgnoreExceptionOnSubModelRecords() {
		$inputData = array(
			'TestPrimary' => array(
				'id' => '1',
				'name' => 'Doe',
				'IgnoreSubRecord' => array(
					0 => array(
						'title' => 'Jane',
						'body' => 'Smith',
						'created' => "created date time"
					),
					1 => array(
						'title' => 'Jane',
						'body' => 'Text',
						'created' => "created date time"
					)
				),
			),
		);

		$expectedOutput = array(
			'test_primary' => array(
				'id' => '1',
				'name' => 'Doe',
			),
		);

		$TestPrimarySerializer = new TestPrimarySerializer();
		$this->assertEquals(
			$expectedOutput,
			$TestPrimarySerializer->serialize($inputData),
			"The output of serialize did not match the the expected output"
		);
	}

}
