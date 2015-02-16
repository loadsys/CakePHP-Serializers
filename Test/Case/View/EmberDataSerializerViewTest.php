<?php
/**
 * Class to the test the EmberDataSerializerView class
 */
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('EmberDataSerializerView', 'Serializers.View');

/**
 * EmberDataSerializerViewTest
 */
class EmberDataSerializerViewTest extends CakeTestCase {

	/**
	 * setup the EmberDataSerializerView when testing
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		$this->EmberDataSerializerView = new EmberDataSerializerView();
		$this->baseUrl = trim(Router::url('/', true), '/');
	}

	/**
	 * tear down the CakeSerializerViewTest when testing
	 *
	 * @return void
	 */
	public function tearDown() {
		unset($this->EmberDataSerializerView);
		unset($this->baseUrl);
		parent::tearDown();
	}

	/**
	 * note that tests for this class are current incomplete
	 *
	 * @return void
	 */
	public function testIncomplete() {
		$this->markTestIncomplete("EmberDataSerializerView currently has no test methods.");
	}

}
