<?php
/*
 * Custom test suite to execute all Serializers Plugin Error tests.
 */
class AllSerializersErrorsTest extends PHPUnit_Framework_TestSuite {
	public static function suite() {
		$suite = new CakeTestSuite('All Serializers Plugin Error Tests');
		$suite->addTestDirectoryRecursive(dirname(__FILE__) . '/Lib/Error/');
		return $suite;
	}
}
