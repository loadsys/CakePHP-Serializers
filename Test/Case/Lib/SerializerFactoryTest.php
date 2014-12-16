<?php

App::uses('SerializerFactory', 'Serializers.Lib');
App::uses('Model', 'Model');

class TestCommentSerializer {
}

class TestTag extends Model {

	public $useTable = false;
}

class SerializerFactoryTest extends CakeTestCase {

	public function testLooksUpConventionallyNamedClasses() {
		$factory = new SerializerFactory('TestComment');
		$this->assertTrue($factory->generate() instanceof TestCommentSerializer);
	}

	public function testGetsDefaultInstanceWhenClassNotDefined() {
		$testTagSchema = array(
			'id' => array(),
			'tag' => array(),
			'created' => array(),
			'modified' => array(),
		);
		$TestTag = $this->getMockForModel('TestTag', array(
			'schema',
		));
		$TestTag->expects($this->any())
			->method('schema')
			->will($this->returnValue($testTagSchema));
		ClassRegistry::addObject('TestTag', $TestTag);
		$factory = new SerializerFactory('TestTag');
		$serializer = $factory->generate();
		$this->assertTrue($serializer instanceof Serializer);
		$this->assertEquals(array_keys($testTagSchema), $serializer->required);
		$this->assertEquals('TestTag', $serializer->rootKey);
	}

	public function testNoModelExistsNorSerializerClassExists() {
		$factory = new SerializerFactory('TestNoModelExistsWithThisName');
		$serializer = $factory->generate();
		$this->assertTrue($serializer instanceof Serializer);
		$this->assertEquals(array(), $serializer->required);
	}
}
