<?php

App::uses('Serializer', 'Serializers.Serializer');
App::uses('Controller', 'Controller');

class RootKeySerializer extends Serializer {
}

class UserSerializer extends Serializer {
	public $attributes = array('first_name', 'last_name');
}

class AfterSerializer extends Serializer {
	public function afterSerialize($data) {
		return "after serialize";
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
}

