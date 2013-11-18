<?php

App::uses('SerializerFactory', 'CakeSerializers.Lib');

class TestPostSerializer {}

class SerializerFactoryTest extends CakeTestCase {
	public function testLooksUpConventionallyNamedClasses() {
		$factory = new SerializerFactory('TestPost');
		$this->assertTrue($factory->generate() instanceof TestPostSerializer);
	}

	public function testThrowsExceptionWhenSerializerDoesNotExist() {
		$factory = new SerializerFactory('MissingPost');
		try {
			$factory->generate();
		} catch (Exception $e) {
			$this->assertRegExp('/Could not find class MissingPostSerializer/', $e->getMessage());
			return;
		}
		$this->assertTrue(false, 'The exception was not thrown for missing class');
	}
}