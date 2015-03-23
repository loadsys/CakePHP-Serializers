<?php
/*
 * Custom test suite to execute all Serializers Plugin Filter tests.
 */
class AllSerializersFiltersTest extends PHPUnit_Framework_TestSuite {
	public static function suite() {
		$suite = new CakeTestSuite('All Serializers Plugin Filter Tests');
		$suite->addTestDirectoryRecursive(dirname(__FILE__) . '/Routing/Filter/');
		return $suite;
	}
}
