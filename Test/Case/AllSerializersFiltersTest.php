<?php
/**
 * Custom test suite to execute all Serializers Plugin Filter tests.
 *
 * @package Serializers.Test.Case
 */

/**
 * AllSerializersFiltersTest
 */
class AllSerializersFiltersTest extends PHPUnit_Framework_TestSuite {

	/**
	 * load the suites
	 *
	 * @return CakeTestSuite
	 */
	public static function suite() {
		$suite = new CakeTestSuite('All Serializers Plugin Filter Tests');
		$suite->addTestDirectoryRecursive(dirname(__FILE__) . '/Routing/Filter/');
		return $suite;
	}
}
