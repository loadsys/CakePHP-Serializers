<?php
/**
 * Custom test suite to execute all Serializers Plugin Serializer tests.
 *
 * @package Serializers.Test.Case
 */

/*
 * AllSerializersSerializersTest
 */
class AllSerializersSerializersTest extends PHPUnit_Framework_TestSuite {

	/**
	 * load the suites
	 *
	 * @return CakeTestSuite
	 */
	public static function suite() {
		$suite = new CakeTestSuite('All Serializers Plugin Serializer Tests');
		$suite->addTestDirectoryRecursive(dirname(__FILE__) . '/Serializer/');
		return $suite;
	}
}
