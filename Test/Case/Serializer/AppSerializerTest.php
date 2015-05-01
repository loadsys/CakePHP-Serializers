<?php
/**
 * Class to test the AppSerializer
 */
App::uses('AppSerializer', 'Serializers.Serializer');

/**
 * AppSerializerTest
 */
class AppSerializerTest extends CakeTestCase {

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array(
	);

	/**
	 * setUp method
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		$this->AppSerializer = new AppSerializer();
	}

	/**
	 * tearDown method
	 *
	 * @return void
	 */
	public function tearDown() {
		unset($this->AppSerializer);

		parent::tearDown();
	}

	/**
	 * test that AppSerializer is an instance of the correct classes
	 *
	 * @return void
	 */
	public function testAppSerializer() {
		$this->assertInstanceOf(
			'Serializer',
			$this->AppSerializer,
			"The TestAppSerializer is not an instance of Serializer"
		);
		$this->assertInstanceOf(
			'AppSerializer',
			$this->AppSerializer,
			"The TestAppSerializer is not an instance of AppSerializer"
		);
	}

}

