<?php
/**
 * Custom test suite to execute all Serializers Plugin Lib tests.
 *
 * @package Serializers.Test.Case
 */

/**
 * AllSerializersLibsTest
 */
class AllSerializersLibsTest extends PHPUnit_Framework_TestSuite {

	/**
	 * load the suites
	 *
	 * @return CakeTestSuite
	 */
	public static function suite() {
		$suite = new CakeTestSuite('All Serializers Plugin Lib Tests');
		$suite->addTestDirectoryRecursive(dirname(__FILE__) . '/Lib/');
		return $suite;
	}
}
