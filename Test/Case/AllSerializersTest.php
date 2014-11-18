<?php
/*
 * Custom test suite to execute all task tests.
 */
class AllSerializersTest extends PHPUnit_Framework_TestSuite {
	public static function suite() {
		$suite = new CakeTestSuite('All Serializers Tests');
		$suite->addTestDirectory(dirname(__FILE__) . '/Lib/');
		$suite->addTestDirectory(dirname(__FILE__) . '/Serializer/');
		$suite->addTestDirectory(dirname(__FILE__) . '/Routing/Filter/');
		return $suite;
	}
}
