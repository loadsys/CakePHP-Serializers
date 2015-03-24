<?php
/*
 * Custom test suite to execute all Serializers Plugin Serializer tests.
 */
class AllSerializersSerializersTest extends PHPUnit_Framework_TestSuite {
	public static function suite() {
		$suite = new CakeTestSuite('All Serializers Plugin Serializer Tests');
		$suite->addTestDirectoryRecursive(dirname(__FILE__) . '/Serializer/');
		return $suite;
	}
}
