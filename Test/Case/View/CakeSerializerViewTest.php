<?php
/**
 * Class to the test the CakeSerializerView class
 *
 * @package Serializers.Test.Case.View
 */
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('CakeSerializerView', 'Serializers.View');

/**
 * TestCakeSerializerView
 */
class TestCakeSerializerView extends CakeSerializerView {

	/**
	 * call parent method
	 *
	 * @return multi response from parent method
	 */
	public function isJsonApiRequest() {
		return parent::isJsonApiRequest();
	}

	/**
	 * call parent method
	 *
	 * @return multi response from parent method
	 */
	public function isJsonRequest() {
		return parent::isJsonRequest();
	}
}


/**
 * CakeSerializerViewTest
 */
class CakeSerializerViewTest extends CakeTestCase {

	/**
	 * setup the CakeSerializerViewTest when testing
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
	}

	/**
	 * tear down the CakeSerializerViewTest when testing
	 *
	 * @return void
	 */
	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * test the isJsonApiRequest method when returns true
	 *
	 * @return void
	 */
	public function testIsJsonApiRequestTrue() {
		$mockController = $this->getMock('Controller', array('render', 'here'));
		$mockController->request = $this->getMock('CakeRequest');
		$mockController->request->expects($this->once())
			->method('accepts')
			->with('application/vnd.api+json')
			->will($this->returnValue(true));
		$mockController->response = new CakeResponse();
		$cakeSerializerView = new TestCakeSerializerView($mockController);
		$this->assertEquals(
			true,
			$cakeSerializerView->isJsonApiRequest(),
			"::cakeSerializerView should have returned true, when we have the accepts header returning true"
		);
	}
	/**
	 * test the isJsonApiRequest method when returns false
	 *
	 * @return void
	 */
	public function testIsJsonApiRequestFalse() {
		$mockController = $this->getMock('Controller', array('render', 'here'));
		$mockController->request = $this->getMock('CakeRequest');
		$mockController->request->expects($this->once())
			->method('accepts')
			->with('application/vnd.api+json')
			->will($this->returnValue(false));
		$mockController->response = new CakeResponse();
		$cakeSerializerView = new TestCakeSerializerView($mockController);
		$this->assertEquals(
			false,
			$cakeSerializerView->isJsonApiRequest(),
			"::isJsonApiRequest should have returned false, when we have the accepts header returning false"
		);
	}
	/**
	 * test the isJsonRequest method when returns true
	 *
	 * @return void
	 */
	public function testIsJsonRequestTrue() {
		$mockController = $this->getMock('Controller', array('render', 'here'));
		$mockController->request = $this->getMock('CakeRequest');
		$mockController->request->expects($this->once())
			->method('accepts')
			->with('application/json')
			->will($this->returnValue(true));
		$mockController->response = new CakeResponse();
		$cakeSerializerView = new TestCakeSerializerView($mockController);
		$this->assertEquals(
			true,
			$cakeSerializerView->isJsonRequest(),
			"::isJsonRequest should have returned true, when we have the accepts header returning true"
		);
	}
	/**
	 * test the isJsonRequest method when returns false
	 *
	 * @return void
	 */
	public function testIsJsonRequestFalse() {
		$mockController = $this->getMock('Controller', array('render', 'here'));
		$mockController->request = $this->getMock('CakeRequest');
		$mockController->request->expects($this->once())
			->method('accepts')
			->with('application/json')
			->will($this->returnValue(false));
		$mockController->response = new CakeResponse();
		$cakeSerializerView = new TestCakeSerializerView($mockController);
		$this->assertEquals(
			false,
			$cakeSerializerView->isJsonRequest(),
			"::isJsonRequest should have returned false, when we have the accepts header returning false"
		);
	}

}
