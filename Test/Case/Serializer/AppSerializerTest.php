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
	 * test that AppSerializer is an instance of the correct classes
	 *
	 * @return void
	 */
	public function testAppSerializer() {
		$TestAppSerializer = new AppSerializer();

		$this->assertInstanceOf(
			'Serializer',
			$TestAppSerializer,
			"The TestAppSerializer is not an instance of Serializer"
		);
		$this->assertInstanceOf(
			'AppSerializer',
			$TestAppSerializer,
			"The TestAppSerializer is not an instance of AppSerializer"
		);
	}

}
