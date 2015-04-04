<?php
/**
 * Class to test the Serialization Class
 *
 * @package Serializers.Test.Lib
 */
App::uses('Serialization', 'Serializers.Lib');
App::uses('Serializer', 'Serializers.Serializer');

/**
 * TestPostSerializer
 */
class TestPostSerializer extends Serializer {

	/**
	 * required properties for the serializer class
	 *
	 * @var array
	 */
	public $required = array('title', 'body', 'summary');
}

/**
 * SerializationTest
 */
class SerializationTest extends CakeTestCase {

	public function testParsesSingleRecord() {
		$data = array('TestPost' => array(
			'title' => 'Title1',
			'body' => 'Body1',
			'summary' => 'Summary',
		));
		$expected = array('test_post' =>
			array(
				'title' => 'Title1',
				'body' => 'Body1',
				'summary' => 'Summary',
			)
		);
		$serialization = new Serialization('TestPost', $data);
		$this->assertEquals($expected, $serialization->serialize());
	}

	public function testDeparseSingleListOfRecords() {
		$data = array('test_posts' => array(
			'title' => 'Title1', 'body' => 'Body1', 'summary' => 'Summary'
		));
		$expected = array(
			'TestPost' => array(
				'title' => 'Title1',
				'body' => 'Body1',
				'summary' => 'Summary',
			),
		);
		$serialization = new Serialization('TestPost', $data);
		$this->assertEquals($expected, $serialization->deserialize());
	}
}
