<?php

App::uses('Serialization', 'Serializers.Lib');
App::uses('Serializer', 'Serializers.Serializer');

class TestPostSerializer extends Serializer {
	public $attributes = array('title', 'body', 'summary');
}

class SerializationTest extends CakeTestCase {
	public function testParsesSingleRecord() {
		$data = array('TestPost' => array(
			'title' => 'Title1',
			'body' => 'Body1'
		));
		$expected = array('test_posts' => array(
			array('title' => 'Title1', 'body' => 'Body1')
		));
		$serialization = new Serialization('TestPost', $data);
		$this->assertEquals($expected, $serialization->parse());
	}

	public function testParsesListOfRecords() {
		$data = array(
			array('TestPost' => array(
				'title' => 'Title1',
				'body' => 'Body1'
			)),
			array('TestPost' => array(
				'title' => 'Title2',
				'body' => 'Body2'
			))
		);
		$expected = array('test_posts' => array(
			array('title' => 'Title1', 'body' => 'Body1'),
			array('title' => 'Title2', 'body' => 'Body2')
		));
		$serialization = new Serialization('TestPost', $data);
		$this->assertEquals($expected, $serialization->parse());
	}
}
