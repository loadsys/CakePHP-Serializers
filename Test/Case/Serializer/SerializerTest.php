<?php

App::uses('Serializer', 'Serializers.Serializer');
App::uses('Controller', 'Controller');

class TestRootKeySerializer extends Serializer {}

class TestUserSerializer extends Serializer {
	public $required = array('first_name', 'last_name');
}

class TestCallbackSerializer extends Serializer {
	public function afterSerialize($json, $record) {
		return "after serialize";
	}

	public function afterDeserialize($data, $json) {
		return "after deserialize";
	}
}

class TestBadOptionalSerializer extends Serializer {
	public $required = array('title', 'body');
	public $optional = 'notanarray';
}

class TestOptionalSerializer extends Serializer {
	public $required = array('title', 'body');
	public $optional = array('summary', 'published');

	public function serialize_body($data, $record) {
		return 'BODY';
	}

	public function serialize_summary($data, $record) {
		return 'SUMMARY';
	}

	public function deserialize_body($data, $record) {
		return 'BODY';
	}

	public function deserialize_summary($data, $record) {
		return 'SUMMARY';
	}
}

class TestMethodOptionalSerializer extends Serializer {
	public $required = array('title', 'body');
	public $optional = array('summary', 'published', 'tags', 'created');

	public function serialize_tags($data, $record) {
		return 'Tags';
	}
}

class TestIgnoreSerializer extends Serializer {
	public $required = array('title', 'body');
	public $optional = array('created');

	public function serialize_created($data, $record) {
		throw new SerializerIgnoreException();
	}

	public function deserialize_created($data, $record) {
		throw new DeserializerIgnoreException();
	}
}

class SerializerTest extends CakeTestCase {
	public function testSerializerRootKeyGeneration() {
		$serializer = new TestRootKeySerializer();
		$this->assertEquals('TestRootKey', $serializer->rootKey);
	}

	public function testSerializerUsesAttributesInAttributesArray() {
		$data = array(
			array('TestUser' => array('first_name' => 'John', 'last_name' => 'Doe'))
		);
		$serializer = new TestUserSerializer();
		$expected = array('test_users' => array(
			array('first_name' => 'John', 'last_name' => 'Doe')
		));
		$this->assertEquals($expected, $serializer->toJsonApi($data));
	}

	public function testDeserializerUsesAttributesInAttributesArray() {
		$expected = array('first_name' => 'John', 'last_name' => 'Doe');
		$serializer = new TestUserSerializer();
		$data = array('test_users' => array(
			'first_name' => 'John', 'last_name' => 'Doe'
		));
		$this->assertEquals($expected, $serializer->fromJsonApi($data));
	}

	public function testSerializerUsesNoDataPassedToTheSerializerArray() {
		$data = array(
		);
		$serializer = new TestUserSerializer();
		$expected = array();
		$this->assertEquals($expected, $serializer->toJsonApi($data));
	}

	public function testDeserializerUsesNoDataPassedToTheSerializerArray() {
		$data = array(
		);
		$serializer = new TestUserSerializer();
		$expected = array();
		$this->assertEquals($expected, $serializer->fromJsonApi($data));
	}

	public function testSerializerUsesEmptyDataPassedToTheSerializerArray() {
		$data = array(
			'TestUser' => array()
		);
		$serializer = new TestUserSerializer();
		$expected = array('test_users' => array(
		));
		$this->assertEquals($expected, $serializer->toJsonApi($data));
	}

	public function testDeserializerUsesEmptyDataPassedToTheSerializerArray() {
		$data = array(
		);
		$serializer = new TestUserSerializer();
		$expected = array();
		$this->assertEquals($expected, $serializer->fromJsonApi($data));
	}

	public function testSerializerAfterSerializeCallback() {
		$serializer = new TestCallbackSerializer();
		$data = array(array("TestCallback" => array()));
		$expected = array("test_callbacks" => array("after serialize"));
		$this->assertEquals($expected, $serializer->toJsonApi($data));
	}

	public function testDeserializerAfterDeserializeCallback() {
		$serializer = new TestCallbackSerializer();
		$data = array('test_callbacks' => array(
			'first_name' => 'John', 'last_name' => 'Doe'
		));
		$expected = "after deserialize";
		$this->assertEquals($expected, $serializer->fromJsonApi($data));
	}

	public function testMissingRequiredAttribute() {
		$data = array(
			array('TestUser' => array('first_name' => 'John'))
		);
		$serializer = new TestUserSerializer();
		$this->setExpectedException(
			'SerializerMissingRequiredException',
			"The following keys were missing from TestUser: last_name"
		);
		$serializer->toJsonApi($data);
	}

	public function testBadOptionalAttributes() {
		$data = array(
			array('TestBadOptional' => array(
				'title' => 'Title',
				'body' => 'Body',
			))
		);
		$serializer = new TestBadOptionalSerializer();
		$expected = array('test_bad_optionals' => array(
			array(
				'title' => 'Title',
				'body' => 'Body',
			)
		));
		$this->assertEquals($expected, $serializer->toJsonApi($data));
	}

	public function testSerializeNoData() {
		$data = null;
		$expected = null;

		$serializer = new TestRootKeySerializer();
		$this->assertEquals($expected, $serializer->toJsonApi($data));
	}

	public function testDeserializeNoData() {
		$data = null;
		$expected = null;

		$serializer = new TestRootKeySerializer();
		$this->assertEquals($expected, $serializer->fromJsonApi($data));
	}

	public function testOptionalIncludedAttributes() {
		$data = array(
			array('TestOptional' => array(
				'title' => 'Title',
				'body' => 'Body',
				'summary' => 'Summary',
				'published' => true
			))
		);
		$serializer = new TestOptionalSerializer();
		$expected = array('test_optionals' => array(
			array(
				'title' => 'Title',
				'body' => 'BODY',
				'summary' => 'SUMMARY',
				'published' => true
			)
		));
		$this->assertEquals($expected, $serializer->toJsonApi($data));
	}

	public function testOptionalExcludedAttributes() {
		$data = array(
			array('TestOptional' => array(
				'title' => 'Title',
				'body' => 'Body',
			))
		);
		$serializer = new TestOptionalSerializer();
		$expected = array('test_optionals' => array(
			array(
				'title' => 'Title',
				'body' => 'BODY',
				'summary' => 'SUMMARY',
			)
		));
		$this->assertEquals($expected, $serializer->toJsonApi($data));
	}

	public function testNonProvidedAttributes() {
		$data = array(
			array('TestOptional' => array(
				'title' => 'Title',
				'body' => 'Body',
				'published' => true,
				'tags' => 'tag1,tag2,tag3',
			))
		);
		$serializer = new TestOptionalSerializer();
		$expected = array('test_optionals' => array(
			array(
				'title' => 'Title',
				'body' => 'BODY',
				'summary' => 'SUMMARY',
				'published' => true
			)
		));
		$this->assertEquals($expected, $serializer->toJsonApi($data));
	}

	public function testCamelCasedNonProvidedAttributes() {
		$data = array(
			array('TestOptional' => array(
				'title' => 'Title',
				'body' => 'Body',
				'published' => true,
				'Tag' => array(
					'name' => 'tag1',
				),
			))
		);
		$serializer = new TestOptionalSerializer();
		$expected = array('test_optionals' => array(
			array(
				'title' => 'Title',
				'body' => 'BODY',
				'summary' => 'SUMMARY',
				'published' => true
			)
		));
		$this->assertEquals($expected, $serializer->toJsonApi($data));
	}

	public function testNotProvidedDataWithMethodOptionalAttribute() {
		$data = array(
			array('TestMethodOptional' => array(
				'title' => 'Title',
				'body' => 'Body',
				'published' => true,
			))
		);
		$serializer = new TestMethodOptionalSerializer();
		$expected = array('test_method_optionals' => array(
			array(
				'title' => 'Title',
				'body' => 'Body',
				'published' => true,
				'tags' => 'Tags',
			)
		));
		$this->assertEquals($expected, $serializer->toJsonApi($data));
	}

	public function testDeserializeDataWithMethod() {
		$expected = array(
			'title' => 'Title',
			'body' => 'BODY',
			'summary' => 'SUMMARY',
			'published' => true,
		);
		$data = array('test_optionals' => array(
			'title' => 'Title',
			'body' => 'Body',
			'summary' => 'Summary',
			'published' => true
		));

		$serializer = new TestOptionalSerializer();
		$this->assertEquals($expected, $serializer->fromJsonApi($data));
	}

	public function testSerializeIgnoreAttribute() {
		$data = array(
			array('TestIgnore' => array(
				'title' => 'Title',
				'body' => 'Body',
				'created' => '2014-07-07',
			))
		);
		$serializer = new TestIgnoreSerializer();
		$expected = array('test_ignores' => array(
			array(
				'title' => 'Title',
				'body' => 'Body',
			)
		));
		$this->assertEquals($expected, $serializer->toJsonApi($data));
	}

	public function testDeserializeIgnoreAttribute() {
		$expected = array(
			'title' => 'Title',
			'body' => 'Body',
		);
		$data = array('test_ignores' => array(
			'title' => 'Title',
			'body' => 'Body',
			'created' => '2014-07-07',
		));

		$serializer = new TestIgnoreSerializer();
		$this->assertEquals($expected, $serializer->fromJsonApi($data));
	}
}

