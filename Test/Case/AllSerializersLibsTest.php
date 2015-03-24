<?php
/*
 * Custom test suite to execute all Serializers Plugin Lib tests.
 */
class AllSerializersLibsTest extends PHPUnit_Framework_TestSuite {
	public static function suite() {
		$suite = new CakeTestSuite('All Serializers Plugin Lib Tests');
		$suite->addTestDirectoryRecursive(dirname(__FILE__) . '/Lib/');
		return $suite;
	}
}
