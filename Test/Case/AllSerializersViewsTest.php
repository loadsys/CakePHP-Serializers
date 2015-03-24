<?php
/*
 * Custom test suite to execute all Serializers Plugin View tests.
 */
class AllSerializersViewsTest extends PHPUnit_Framework_TestSuite {
	public static function suite() {
		$suite = new CakeTestSuite('All Serializers Plugin View Tests');
		$suite->addTestDirectoryRecursive(dirname(__FILE__) . '/View/');
		return $suite;
	}
}
