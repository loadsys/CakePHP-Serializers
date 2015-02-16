<?php
/**
 * Class to the test the CakeSerializerView class
 */
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('CakeSerializerView', 'Serializers.View');

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
		$this->CakeSerializerView = new CakeSerializerView();
		$this->baseUrl = trim(Router::url('/', true), '/');
	}

	/**
	 * tear down the CakeSerializerViewTest when testing
	 *
	 * @return void
	 */
	public function tearDown() {
		unset($this->CakeSerializerView);
		unset($this->baseUrl);
		parent::tearDown();
	}

	/**
	 * note that tests for this class are current incomplete
	 *
	 * @return void
	 */
	public function testIncomplete() {
		$this->markTestIncomplete("CakeSerializerView currently has no test methods.");
	}

}
