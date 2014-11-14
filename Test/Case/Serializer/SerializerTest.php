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
		return strtoupper($data['body']);
	}

	public function summary($data, $record) {
		return strtoupper($data['summary']);
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
				'published' => true
			))
		);
		$serializer = new OptionalSerializer();
		$expected = array('optionals' => array(
			array(
				'title' => 'Title',
				'body' => 'BODY',
				'published' => true
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
				'published' => true
			)
		));
		$this->assertEquals($expected, $serializer->toArray($data));
	}
}

