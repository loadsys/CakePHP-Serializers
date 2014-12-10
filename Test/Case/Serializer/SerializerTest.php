<?php
/**
 * Class to test the serialization methods
 */
App::uses('Serializer', 'Serializers.Serializer');
App::uses('Controller', 'Controller');
require_once( dirname(__FILE__) . '/serializer_test_classes.php');

class SerializerTest extends CakeTestCase {

	public function testRootKeyGeneration() {
		$serializer = new TestRootKeySerializer();
		$this->assertEquals('TestRootKey', $serializer->rootKey);
	}

	public function testSerializerUsesAttributesInAttributesArray() {
		$data = array(
			'TestUser' => array(
				'first_name' => 'John',
				'last_name' => 'Doe'
			)
		);
		$serializer = new TestUserSerializer();
		$expected = array(
			'test_users' => array(
				'first_name' => 'John',
				'last_name' => 'Doe'
			)
		);
		$this->assertEquals($expected, $serializer->serialize($data));
	}

	public function testSerializerUsesNoDataPassedToTheSerializerArray() {
		$data = array(
		);
		$serializer = new TestUserSerializer();
		$expected = array();
		$this->assertEquals($expected, $serializer->serialize($data));
	}

	public function testSerializerUsesEmptyDataPassedToTheSerializerArray() {
		$data = array(
			'TestUser' => array()
		);
		$serializer = new TestUserSerializer();
		$expected = array('test_users' => array(
		));
		$this->assertEquals($expected, $serializer->serialize($data));
	}

	public function testSerializerAfterSerializeCallback() {
		$serializer = new TestCallbackSerializer();
		$data = array(array("TestCallback" => array()));
		$expected = "after serialize";
		$this->assertEquals($expected, $serializer->serialize($data));
	}

	public function testMissingRequiredAttribute() {
		$data = array(
			'TestUser' => array(
				'first_name' => 'John'
			)
		);
		$serializer = new TestUserSerializer();
		$this->setExpectedException(
			'SerializerMissingRequiredException',
			"The following keys were missing from TestUser: last_name"
		);
		$serializer->serialize($data);
	}

	public function testBadOptionalAttributes() {
		$data = array(
			'TestBadOptional' => array(
				'title' => 'Title',
				'body' => 'Body',
			)
		);
		$serializer = new TestBadOptionalSerializer();
		$expected = array('test_bad_optionals' => array(
			'title' => 'Title',
			'body' => 'Body',
		));
		$this->assertEquals($expected, $serializer->serialize($data));
	}

	public function testSerializeNoData() {
		$data = null;
		$expected = null;

		$serializer = new TestRootKeySerializer();
		$this->assertEquals($expected, $serializer->serialize($data));
	}

	public function testSerializeOptionalIncludedAttributes() {
		$data = array(
			'TestOptional' => array(
				'title' => 'Title',
				'body' => 'Body',
				'summary' => 'Summary',
				'published' => true
			)
		);
		$serializer = new TestOptionalSerializer();
		$expected = array('test_optionals' => array(
			'title' => 'Title',
			'body' => 'BODY',
			'summary' => 'SUMMARY',
			'published' => true
		));
		$this->assertEquals($expected, $serializer->serialize($data));
	}

	public function testSerializeOptionalExcludedAttributes() {
		$data = array(
			'TestOptional' => array(
				'title' => 'Title',
				'body' => 'Body',
			)
		);
		$serializer = new TestOptionalSerializer();
		$expected = array('test_optionals' => array(
			'title' => 'Title',
			'body' => 'BODY',
		));
		$this->assertEquals($expected, $serializer->serialize($data));
	}

	public function testSerializeNonProvidedAttributes() {
		$data = array(
			'TestOptional' => array(
				'title' => 'Title',
				'body' => 'Body',
				'published' => true,
				'tags' => 'tag1,tag2,tag3',
			)
		);
		$serializer = new TestOptionalSerializer();
		$expected = array('test_optionals' => array(
			'title' => 'Title',
			'body' => 'BODY',
			'published' => true
		));
		$this->assertEquals($expected, $serializer->serialize($data));
	}

	public function testSerializeAttributesWithMethod() {
		$data = array(
			'TestMethodOptional' => array(
				'title' => 'Title',
				'body' => 'Body',
				'published' => true,
				'tags' => 'tag1,tag2,tag3',
			)
		);
		$serializer = new TestMethodOptionalSerializer();
		$expected = array('test_method_optionals' => array(
			'title' => 'Title',
			'body' => 'Body',
			'published' => true,
			'tags' => 'Tags',
		));
		$this->assertEquals($expected, $serializer->serialize($data));
	}

	public function testSerializeNotProvidedDataWithMethodOptionalAttribute() {
		$data = array(
			'TestMethodOptional' => array(
				'title' => 'Title',
				'body' => 'Body',
				'published' => true,
			)
		);
		$serializer = new TestMethodOptionalSerializer();
		$expected = array('test_method_optionals' => array(
				'title' => 'Title',
				'body' => 'Body',
				'published' => true,
		));
		$this->assertEquals($expected, $serializer->serialize($data));
	}

	public function testSerializeIgnoreAttribute() {
		$data = array(
			'TestIgnore' => array(
				'title' => 'Title',
				'body' => 'Body',
				'created' => '2014-07-07',
			)
		);
		$serializer = new TestIgnoreSerializer();
		$expected = array('test_ignores' => array(
			'title' => 'Title',
			'body' => 'Body',
		));
		$this->assertEquals($expected, $serializer->serialize($data));
	}

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
			'test_users' =>
			array(
				'first_name' => 'John',
				'last_name' => 'Doe',
				'test_second_level_users' => array(
					'first_name' => 'Jane', 'last_name' => 'Doe',
				),
			),
		);
		$serializer = new TestUserSerializer();
		$this->assertEquals($expectedOutput, $serializer->serialize($inputData));
	}

	public function testSerializeSubModelRecordsWithAttributeMethod() {
		$expectedOutput = array(
			'test_users' =>
			array(
				'first_name' => 'John',
				'last_name' => 'Doe',
				'test_second_level_user_with_methods' => array(
					'first_name' => 'FIRST',
					'last_name' => 'Doe',
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
			'test_users' =>
			array(
				'first_name' => 'John',
				'last_name' => 'Doe',
				'test_second_level_users' => array(
					'first_name' => 'Jane', 'last_name' => 'Smith',
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
			'test_users' => array(
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
						'first_name' => 'Someone',
						'last_name' => 'THings',
					),
				),
				1 => array(
					'first_name' => 'Jane',
					'last_name' => 'Smith',
					'test_second_level_users' => array(
						'first_name' => 'Random',
						'last_name' => 'Person',
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
						'first_name' => 'Someone',
						'last_name' => 'THings',
					),
				),
				1 => array(
					'first_name' => 'Jane',
					'last_name' => 'Smith',
					'test_second_level_users' => array(
						'first_name' => 'Random',
						'last_name' => 'Person',
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
			'test_users' => array(
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

}
