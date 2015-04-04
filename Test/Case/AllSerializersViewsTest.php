<?php
/**
 * Custom test suite to execute all Serializers Plugin View tests.
 *
 * @package Serializers.Test.Case
 */

/**
 * AllSerializersViewsTest
 */
class AllSerializersViewsTest extends PHPUnit_Framework_TestSuite {

	/**
	 * load the suites
	 *
	 * @return CakeTestSuite
	 */
	public static function suite() {
		$suite = new CakeTestSuite('All Serializers Plugin View Tests');
		$suite->addTestDirectoryRecursive(dirname(__FILE__) . '/View/');
		return $suite;
	}
}
