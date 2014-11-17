<?php

App::uses('Serializer', 'Serializers.Serializer');
App::uses('Controller', 'Controller');

class TestRootKeySerializer extends Serializer {}

class TestUserSerializer extends Serializer {
	public $required = array('first_name', 'last_name');
}

class TestAfterSerializer extends Serializer {
	public function afterSerialize($json, $record) {
		return "after serialize";
	}
}

class TestOptionalSerializer extends Serializer {
	public $required = array('title', 'body');
	public $optional = array('summary', 'published');

	public function body($data, $record) {
		return 'BODY';
	}

	public function summary($data, $record) {
		return 'SUMMARY';
	}
}

class TestMethodOptionalSerializer extends Serializer {
	public $required = array('title', 'body');
	public $optional = array('summary', 'published', 'tags', 'created');

	public function tags($data, $record) {
		return 'Tags';
	}
}

class TestIgnoreOptionalSerializer extends Serializer {
	public $required = array('title', 'body');
	public $optional = array('created');

	public function created($data, $record) {
		throw new SerializerIgnoreException();
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
		$this->assertEquals($expected, $serializer->toArray($data));
	}

	public function testSerializerUsesNoDataPassedToTheSerializerArray() {
		$data = array(
		);
		$serializer = new TestUserSerializer();
		$expected = array();
		$this->assertEquals($expected, $serializer->toArray($data));
	}

	public function testSerializerUsesEmptyDataPassedToTheSerializerArray() {
		$data = array(
			'TestUser' => array()
		);
		$serializer = new TestUserSerializer();
		$expected = array('test_users' => array(
		));
		$this->assertEquals($expected, $serializer->toArray($data));
	}

	public function testSerializerAfterSerializeCallback() {
		$serializer = new TestAfterSerializer();
		$data = array(array("TestAfter" => array()));
		$expected = array("test_afters" => array("after serialize"));
		$this->assertEquals($expected, $serializer->toArray($data));
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
		$serializer->toArray($data);
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
		$this->assertEquals($expected, $serializer->toArray($data));
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
		$this->assertEquals($expected, $serializer->toArray($data));
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
		$this->assertEquals($expected, $serializer->toArray($data));
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
		$this->assertEquals($expected, $serializer->toArray($data));
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
		$this->assertEquals($expected, $serializer->toArray($data));
	}

	public function testIgnoreOptionalAttribute() {
		$data = array(
			array('TestIgnoreOptional' => array(
				'title' => 'Title',
				'body' => 'Body',
				'created' => '2014-07-07',
			))
		);
		$serializer = new TestIgnoreOptionalSerializer();
		$expected = array('test_ignore_optionals' => array(
			array(
				'title' => 'Title',
				'body' => 'Body',
			)
		));
		$this->assertEquals($expected, $serializer->toArray($data));
	}
}

