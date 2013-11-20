<?php

App::uses('CheckRequest', 'CakeSerializers.Lib');

class CheckRequestTest extends CakeTestCase {
	public $controller;
	public $checkRequest;

	public function setUp() {
		$this->controller = $this->getMock('stdClass');
		$request = $this->getMock('stdClass', array('accepts', 'header'));
		$request->url = 'http://example.com';
		$this->controller->request = $request;
		$this->checkRequest = new CheckRequest($this->controller);
	}

	public function tearDown() {
		unset($this->controller, $this->checkRequest);
	}

	public function testIsJsonTrueWhenControllerRenderAsPropertyIsJson() {
		$this->controller->renderAs = 'json';
		$this->assertTrue($this->checkRequest->isJson());
	}

	public function testIsJsonFalseWhenControllerRenderAsPropertyIsNotJson() {
		$this->controller->renderAs = 'html';
		$this->assertFalse($this->checkRequest->isJson());
	}

	public function testIsJsonTrueWhenRequestContentTypeIsAJsonType() {
		$this->controller->request
		                 ->expects($this->once())
		                 ->method('header')
		                 ->with($this->equalTo('Content-Type'))
		                 ->will($this->returnValue('application/json'));
		$this->assertTrue($this->checkRequest->isJson());
	}

	public function testIsJsonTrueWhenRequestAcceptsHeaderIsAJsonType() {
		$this->controller->request
		                 ->expects($this->once())
		                 ->method('accepts')
		                 ->will($this->returnValue(array('application/json')));
		$this->assertTrue($this->checkRequest->isJson());
	}

	public function testIsJsonTrueWhenRequestExtensionIsAJsonType() {
		$this->controller->request->url = 'http://example.com/api/posts.json';
		$this->assertTrue($this->checkRequest->isJson());
	}

	public function testIsJsonFalseWhenRequestPartsAreNotJson() {
		unset($this->controller->renderAs);
		$this->controller->request
		                 ->expects($this->once())
		                 ->method('header')
		                 ->with($this->equalTo('Content-Type'))
		                 ->will($this->returnValue('text/html'));
		$this->controller->request
		                 ->expects($this->once())
		                 ->method('accepts')
		                 ->will($this->returnValue(array('text/html')));
		$this->assertFalse($this->checkRequest->isJson());
	}
}
