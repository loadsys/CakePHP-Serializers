<?php

App::uses('AnalyzeRequest', 'CakeSerializers.Lib');

class AnalyzeRequestTest extends CakeTestCase {
	public $analyzer;

	public function setUp() {
		$this->request = $this->getMock('stdClass', array('accepts', 'header'));
		$this->request->url = 'http://example.com';
		$this->analyzer = new AnalyzeRequest($this->request);
	}

	public function tearDown() {
		unset($this->request, $this->analyzer);
	}

	public function testIsJsonTrueWhenRequestContentTypeIsAJsonType() {
		$this->request
		     ->expects($this->once())
		     ->method('header')
		     ->with($this->equalTo('Content-Type'))
		     ->will($this->returnValue('application/json'));
		$this->assertTrue($this->analyzer->isJson());
	}

	public function testIsJsonTrueWhenRequestAcceptsHeaderIsAJsonType() {
		$this->request
		     ->expects($this->once())
		     ->method('accepts')
		     ->will($this->returnValue(array('application/json')));
		$this->assertTrue($this->analyzer->isJson());
	}

	public function testIsJsonTrueWhenRequestExtensionIsAJsonType() {
		$this->request->url = 'http://example.com/api/posts.json';
		$this->assertTrue($this->analyzer->isJson());
	}

	public function testIsJsonFalseWhenRequestPartsAreNotJson() {
		$this->request
		     ->expects($this->once())
		     ->method('header')
		     ->with($this->equalTo('Content-Type'))
		     ->will($this->returnValue('text/html'));
		$this->request
		     ->expects($this->once())
		     ->method('accepts')
		     ->will($this->returnValue(array('text/html')));
		$this->assertFalse($this->analyzer->isJson());
	}
}
