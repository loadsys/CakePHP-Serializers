<?php
/**
 * Class to test the serialization root key generation
 */
App::uses('Serializer', 'Serializers.Serializer');
App::uses('Controller', 'Controller');
require_once( dirname(__FILE__) . '/serializer_test_classes.php');

/**
 * SerializerRootKeyTest
 */
class SerializerRootKeyTest extends CakeTestCase {

	/**
	 * test the RootKey Generates correctly
	 *
	 * @return void
	 */
	public function testRootKeyGeneration() {
		$Serializer = new TestRootKeySerializer();
		$this->assertEquals(
			'TestRootKey',
			$Serializer->rootKey,
			"The Generated RootKey does not equal the name of the Class"
		);
	}

}
