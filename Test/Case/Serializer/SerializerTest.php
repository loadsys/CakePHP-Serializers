<?php

App::uses('Serializer', 'Serializers.Serializer');
App::uses('Controller', 'Controller');

class RootKeySerializer extends Serializer {}

class UserSerializer extends Serializer {
	public $attributes = array('first_name', 'last_name');
}

class AfterSerializer extends Serializer {
	public function afterSerialize($json, $record) {
		return "after serialize";
	}
}

class OptionalSerializer extends Serializer {
	public $attributes = array('title', 'body');
	public $optional = array('summary', 'published');

	public function body($data, $record) {
		return 'BODY';
	}

	public function summary($data, $record) {
		return 'SUMMARY';
	}
}

class TestMethodOptionalSerializer extends Serializer {
	public $attributes = array('title', 'body');
	public $optional = array('summary', 'published', 'tags', 'created');

	public function tags($data, $record) {
		return 'Tags';
	}
}

class TestIgnoreOptionalSerializer extends Serializer {
	public $attributes = array('title', 'body');
	public $optional = array('created');

	public function created($data, $record) {
		throw new SerializerIgnoreException();
	}
}

class SerializerTest extends CakeTestCase {
	public function testSerializerRootKeyGeneration() {
		$serializer = new RootKeySerializer();
		$this->assertEquals('RootKey', $serializer->rootKey);
	}

	public function testSerializerUsesAttributesInAttributesArray() {
		$data = array(
			array('User' => array('first_name' => 'John', 'last_name' => 'Doe'))
		);
		$serializer = new UserSerializer();
		$expected = array('users' => array(
			array('first_name' => 'John', 'last_name' => 'Doe')
		));
		$this->assertEquals($expected, $serializer->toArray($data));
	}

	public function testSerializerAfterSerializeCallback() {
		$serializer = new AfterSerializer();
		$data = array(array("After" => array()));
		$expected = array("afters" => array("after serialize"));
		$this->assertEquals($expected, $serializer->toArray($data));
	}

	public function testMissingRequiredAttribute() {
		$data = array(
			array('User' => array('first_name' => 'John'))
		);
		$serializer = new UserSerializer();
		$this->setExpectedException(
			'SerializerMissingRequiredException',
			"The following keys were missing from User: last_name"
		);
		$serializer->toArray($data);
	}

	public function testOptionalIncludedAttributes() {
		$data = array(
			array('Optional' => array(
				'title' => 'Title',
				'body' => 'Body',
				'summary' => 'Summary',
				'published' => true
			))
		);
		$serializer = new OptionalSerializer();
		$expected = array('optionals' => array(
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
			array('Optional' => array(
				'title' => 'Title',
				'body' => 'Body',
			))
		);
		$serializer = new OptionalSerializer();
		$expected = array('optionals' => array(
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
			array('Optional' => array(
				'title' => 'Title',
				'body' => 'Body',
				'published' => true,
				'tags' => 'tag1,tag2,tag3',
			))
		);
		$serializer = new OptionalSerializer();
		$expected = array('optionals' => array(
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
			array('Optional' => array(
				'title' => 'Title',
				'body' => 'Body',
				'published' => true,
				'Tag' => array(
					'name' => 'tag1',
				),
			))
		);
		$serializer = new OptionalSerializer();
		$expected = array('optionals' => array(
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

