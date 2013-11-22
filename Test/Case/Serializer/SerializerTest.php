<?php

App::uses('Serializer', 'CakeSerializers.Serializer');
App::uses('Controller', 'Controller');

class RootKeySerializer extends Serializer {
}

class UserSerializer extends Serializer {
	public $attributes = array('first_name', 'last_name');
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
}

