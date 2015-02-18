<?php
/*
 * Custom test suite to execute all task tests.
 */
class AllSerializersTest extends PHPUnit_Framework_TestSuite {

	/**
	 * loads the suite of tests for AllSerializers
	 *
	 * @return array
	 */
	public static function suite() {
		$suite = new CakeTestSuite('All Serializers Tests');
		$suite->addTestDirectoryRecursive(dirname(__FILE__) . '/Lib/');
		$suite->addTestDirectoryRecursive(dirname(__FILE__) . '/Serializer/');
		$suite->addTestDirectoryRecursive(dirname(__FILE__) . '/View/');
		$suite->addTestDirectoryRecursive(dirname(__FILE__) . '/Routing/');
		return $suite;
	}
}
