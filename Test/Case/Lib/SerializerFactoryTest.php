<?php

App::uses('SerializerFactory', 'Serializers.Lib');

class TestCommentSerializer {}

class SerializerFactoryTest extends CakeTestCase {
	public function testLooksUpConventionallyNamedClasses() {
		$factory = new SerializerFactory('TestComment');
		$this->assertTrue($factory->generate() instanceof TestCommentSerializer);
	}

	public function testThrowsExceptionWhenSerializerDoesNotExist() {
		$factory = new SerializerFactory('MissingComment');
		try {
			$factory->generate();
		} catch (Exception $e) {
			$this->assertRegExp('/Could not find class MissingCommentSerializer/', $e->getMessage());
			return;
		}
		$this->assertTrue(false, 'The exception was not thrown for missing class');
	}
}
