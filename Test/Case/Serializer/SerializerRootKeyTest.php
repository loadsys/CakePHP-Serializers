<?php
/**
 * Class to test the serialization root key generation
 */
App::uses('Serializer', 'Serializers.Serializer');
App::uses('Controller', 'Controller');
require_once dirname(__FILE__) . '/serializer_test_classes.php';

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
		$TestRootKeySerializer = new TestRootKeySerializer();
		$this->assertEquals(
			'TestRootKey',
			$TestRootKeySerializer->rootKey,
			"The Generated RootKey does not equal the name of the Class"
		);
	}

	/**
	 * test the RootKey is overridden correctly with a Serialize class that
	 * sets it's own rootKey value
	 *
	 * @return void
	 */
	public function testRootKeyGenerationWithAnOverriddenRootKey() {
		$TestChangedRootKeySerializer = new TestChangedRootKeySerializer();
		$this->assertEquals(
			'changed-root-key',
			$TestChangedRootKeySerializer->rootKey,
			"The RootKey does not equal what the overridden class variable"
		);
	}

}
