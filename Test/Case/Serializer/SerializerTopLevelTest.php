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
	 * test Serializing when null input data to the Serialize method
	 *
	 * @return void
	 */
	public function testSerializeNullData() {
		$data = null;
		$expected = array(
			'test_root_keys' => array(),
		);

		$TestRootKeySerializer = new TestRootKeySerializer();
		$this->assertEquals(
			$expected,
			$TestRootKeySerializer->serialize($data),
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

	/**
	 * test a bad optional class property, with only required data in the input
	 *
	 * @return void
	 */
	public function testBadOptionalAttributesWithOnlyRequiredDataInTheInput() {
		$data = array(
			'TestBadOptional' => array(
				'title' => 'Title',
				'body' => 'Body',
			)
		);
		$expected = array('test_bad_optional' => array(
			'title' => 'Title',
			'body' => 'Body',
		));

		$TestBadOptionalSerializer = new TestBadOptionalSerializer();
		$this->assertEquals(
			$expected,
			$TestBadOptionalSerializer->serialize($data),
			'The output of serialize did not match the expected output'
		);
	}

	/**
	 * test a bad optional class property, with additional data in the input
	 *
	 * @return void
	 */
	public function testBadOptionalAttributesWithAdditionalDataInTheInput() {
		$data = array(
			'TestBadOptional' => array(
				'title' => 'Title',
				'body' => 'Body',
				'something' => 'random',
			)
		);
		$expected = array('test_bad_optional' => array(
			'title' => 'Title',
			'body' => 'Body',
		));

		$TestBadOptionalSerializer = new TestBadOptionalSerializer();
		$this->assertEquals(
			$expected,
			$TestBadOptionalSerializer->serialize($data),
			'The output of serialize did not match the expected output'
		);
	}

	/**
	 * test serializing optional attributes, when the optional data is included
	 *
	 * @return void
	 */
	public function testSerializeOptionalIncludedAttributes() {
		$data = array(
			'TestOptional' => array(
				'title' => 'Title',
				'body' => 'Body',
				'summary' => 'Summary',
				'published' => true
			)
		);
		$expected = array('test_optional' => array(
			'title' => 'Title',
			'body' => 'BODY',
			'summary' => 'SUMMARY',
			'published' => true
		));

		$TestOptionalSerializer = new TestOptionalSerializer();
		$this->assertEquals(
			$expected,
			$TestOptionalSerializer->serialize($data),
			"The output of serialize did not match the expected output"
		);
	}

	/**
	 * test serializing optional attributes, when the optional data is excluded
	 *
	 * @return void
	 */
	public function testSerializeOptionalExcludedAttributes() {
		$data = array(
			'TestOptional' => array(
				'title' => 'Title',
				'body' => 'Body',
			)
		);
		$expected = array('test_optional' => array(
			'title' => 'Title',
			'body' => 'BODY',
		));

		$TestOptionalSerializer = new TestOptionalSerializer();
		$this->assertEquals(
			$expected,
			$TestOptionalSerializer->serialize($data),
			"The output of serialize did not match the expected output"
		);
	}

	/**
	 * test serializing attributes that are not set as either a required or optional
	 * attribute of the class
	 *
	 * @return void
	 */
	public function testSerializeNonProvidedAttributes() {
		$data = array(
			'TestOptional' => array(
				'title' => 'Title',
				'body' => 'Body',
				'published' => true,
				'tags' => 'tag1,tag2,tag3',
			)
		);
		$expected = array('test_optional' => array(
			'title' => 'Title',
			'body' => 'BODY',
			'published' => true
		));

		$TestOptionalSerializer = new TestOptionalSerializer();
		$this->assertEquals(
			$expected,
			$TestOptionalSerializer->serialize($data),
			"The output of serialize did not match the expected output"
		);
	}

	/**
	 * test serializing non provided optional data that has a method associated
	 * with it, the method should not fire when no data was passed for it
	 *
	 * @return void
	 */
	public function testSerializeNotProvidedDataWithMethodOptionalAttribute() {
		$data = array(
			'TestMethodOptional' => array(
				'title' => 'Title',
				'body' => 'Body',
				'published' => true,
			)
		);
		$expected = array('test_method_optional' => array(
				'title' => 'Title',
				'body' => 'Body',
				'published' => true,
		));

		$TestMethodOptionalSerializer = new TestMethodOptionalSerializer();
		$this->assertEquals(
			$expected,
			$TestMethodOptionalSerializer->serialize($data),
			"The output of serialize did not match the expected output"
		);
	}

	/**
	 * test serializing provided optional data that has a method associated with it
	 *
	 * @return void
	 */
	public function testSerializeAttributesWithMethod() {
		$data = array(
			'TestMethodOptional' => array(
				'title' => 'Title',
				'body' => 'Body',
				'published' => true,
				'tags' => 'tag1,tag2,tag3',
			)
		);
		$expected = array('test_method_optional' => array(
			'title' => 'Title',
			'body' => 'Body',
			'published' => true,
			'tags' => 'Tags',
		));

		$TestMethodOptionalSerializer = new TestMethodOptionalSerializer();
		$this->assertEquals(
			$expected,
			$TestMethodOptionalSerializer->serialize($data),
			"The output of serialize did not match the expected output, the method for tags was not called"
		);
	}

	/**
	 * test serializing provided optional data that should ignore the data
	 *
	 * @return void
	 */
	public function testSerializeIgnoreAttribute() {
		$data = array(
			'TestIgnore' => array(
				'title' => 'Title',
				'body' => 'Body',
				'created' => '2014-07-07',
			)
		);
		$expected = array('test_ignore' => array(
			'title' => 'Title',
			'body' => 'Body',
		));

		$TestIgnoreSerializer = new TestIgnoreSerializer();
		$this->assertEquals(
			$expected,
			$TestIgnoreSerializer->serialize($data),
			"The output of serialize did not match the expected output, ignoring of `created` did not occur"
		);
	}

}
