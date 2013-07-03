<?php

App::uses('Serializer', 'CakeSerializers.Serializer');
App::uses('Controller', 'Controller');

class SerializerTest extends CakeTestCase {
	public function setUp() {
		$this->controller = $this->getMockBuilder('Controller')
			->disableOriginalConstructor()
			->getMock();
	}

	public function tearDown() {
		unset($this->controller);
	}

	public function testSerializerExpectsControllerInConstructor() {
		try {
			$serializer = new Serializer();
		} catch (Exception $e) {
			$this->assertRegExp('/instance of Controller/', $e->getMessage());
		}
	}

	public function testSerializerShouldImplementAnAttributesArray() {
		$serializer = new Serializer($this->controller);	
		$this->assertTrue(property_exists($serializer, 'attributes'));
		$this->assertTrue(is_array($serializer->attributes));
	}

	public function testSerializerGetsDefaultValuesFromController() {
		$this->controller->name = 'Posts';
		$serializer = new Serializer($this->controller);	
		$this->assertEquals('posts', $serializer->root);
	}

	public function testEncodeWrapsJsonEncode() {
		$serializer = new Serializer($this->controller);	
		$this->assertEquals('{"key":"value"}', $serializer->encode(array('key' => 'value')));
	}

	public function testToJsonConvertsSingleRecordToJsonString() {
		$this->controller->name = 'Posts';
		$serializer = new Serializer($this->controller);	
		$serializer->attributes = array('id', 'title', 'body');
		$data = array('Post' => array(
			'id' => 1,
			'title' => 'Test Title',
			'body' => 'Content body'
		));
		$value = $serializer->toJson($data);
		$expected = '{"post":{"id":1,"title":"Test Title","body":"Content body"}}';
		$this->assertEquals($expected, $value);
	}

	public function testToJsonConvertsMultipleRecordsToJsonString() {
		$this->controller->name = 'Posts';
		$serializer = new Serializer($this->controller);	
		$serializer->attributes = array('id', 'title', 'body');
		$data = array(
			array('Post' => array(
				'id' => 1,
				'title' => 'Test Title',
				'body' => 'Content body'
			)),
			array('Post' => array(
				'id' => 2,
				'title' => 'Other Title',
				'body' => 'Content other'
			))
		);
		$value = $serializer->toJson($data);
		$expected  = '{"posts":[';
		$expected .= '{"id":1,"title":"Test Title","body":"Content body"},';
		$expected .= '{"id":2,"title":"Other Title","body":"Content other"}';
		$expected .= ']}';
		$this->assertEquals($expected, $value);
	}

	public function testToJsonConvertsSingleRecordWithNoRoot() {
		$this->controller->name = 'Posts';
		$serializer = new Serializer($this->controller, array('root' => false));	
		$serializer->attributes = array('title', 'body');
		$data = array('Post' => array(
			'title' => 'Test Title',
			'body' => 'Content body'
		));
		$value = $serializer->toJson($data);
		$expected = '{"title":"Test Title","body":"Content body"}';
		$this->assertEquals($expected, $value);
	}

	public function testToJsonConvertsMultipleRecordsWithNoRoot() {
		$this->controller->name = 'Posts';
		$serializer = new Serializer($this->controller, array('root' => false));	
		$serializer->attributes = array('id', 'title');
		$data = array(
			array('Post' => array(
				'id' => 1,
				'title' => 'Test Title'
			)),
			array('Post' => array(
				'id' => 2,
				'title' => 'Other Title'
			))
		);
		$value = $serializer->toJson($data);
		$expected  = '[';
		$expected .= '{"id":1,"title":"Test Title"},';
		$expected .= '{"id":2,"title":"Other Title"}';
		$expected .= ']';
		$this->assertEquals($expected, $value);
	}

	public function testToJsonConvertsRespectsCustomRootStrings() {
		$this->controller->name = 'Posts';
		$serializer = new Serializer($this->controller, array('root' => 'my_posts'));
		$serializer->attributes = array('id', 'title', 'body');
		$data = array('Post' => array(
			'id' => 1,
			'title' => 'Test Title',
			'body' => 'Content body'
		));
		$value = $serializer->toJson($data);
		$expected = '{"my_posts":{"id":1,"title":"Test Title","body":"Content body"}}';
		$this->assertEquals($expected, $value);
	}

	public function testToJsonCanPrettyPrint() {
		$this->controller->name = 'Posts';
		$serializer = new Serializer($this->controller, array('pretty' => true));
		$serializer->attributes = array('id', 'title', 'body');
		$data = array('Post' => array(
			'id' => 1,
			'title' => 'Test Title',
			'body' => 'Content body'
		));
		$value = $serializer->toJson($data);
		$expected = "{\n    \"post\": {\n        \"id\": 1,\n        \"title\": \"Test Title\",\n        \"body\": \"Content body\"\n    }\n}";
		$this->assertEquals($expected, $value);
	}
}

