<?php

App::uses('SerializeComponent', 'CakeSerializers.Controller/Component');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('ComponentCollection', 'Controller');

class SerializeComponentTestController extends Controller {
}

class TestSerializer {
	public function toJson() {
		return 'TestSerializer toJson';
	}
}

class SerializeComponentTest extends CakeTestCase {
	public function setUp() {
		$request = new CakeRequest('/');
		$response = new CakeResponse();
		$this->Controller = new SerializeComponentTestController($request, $response);
		$this->Controller->constructClasses();
		$this->Serialize = new SerializeComponent($this->Controller->Components);
		$this->Serialize->startup($this->Controller);
		parent::setUp();
	}

	public function tearDown() {
		unset($this->Controller, $this->Serializer);
		parent::tearDown();
	}

	public function testSerializerNameTransformsPassedInName() {
		$this->assertEquals('ArticleSerializer', $this->Serialize->serializerName('Article'));
		$this->assertEquals('CommentSerializer', $this->Serialize->serializerName('CommentSerializer'));
	}

	public function testSerializerNameUsesNameSetByWith() {
		$this->Serialize->with('Companies');
		$this->assertEquals('CompaniesSerializer', $this->Serialize->serializerName());
		$this->Serialize->with('VendorsSerializer');
		$this->assertEquals('VendorsSerializer', $this->Serialize->serializerName());
	}

	public function testSerializerNameUsesControllerNameByDefault() {
		$this->assertEquals('SerializeComponentTestSerializer', $this->Serialize->serializerName());
	}

	public function testToJsonUsesASerializerInstanceAndDelegatesToJson() {
		$this->assertEquals('TestSerializer toJson', $this->Serialize->toJson(array(), array('serializer' => 'Test')));
	}
}

