<?php
/**
 * Custom test suite to execute all Serializers Plugin tests.
 *
 * @package Serializers.Test.Case
 */

/**
 * AllSerializersTest
 */
class AllSerializersTest extends PHPUnit_Framework_TestSuite {

	/**
	 * the suites to load
	 * @var array
	 */
	public static $suites = array(
		// Lib Folder
		'AllSerializersErrorsTest.php',
		'AllSerializersLibsTest.php',

		// Routing Folder
		'AllSerializersFiltersTest.php',

		// Serializer Folder
		'AllSerializersSerializersTest.php',

		// View Folder
		'AllSerializersViewsTest.php',
	);

	/**
	 * load the suites
	 *
	 * @return CakeTestSuite
	 */
	public static function suite() {
		$path = dirname(__FILE__) . '/';
		$suite = new CakeTestSuite('All Tests');

		foreach (self::$suites as $file) {
			if (is_readable($path . $file)) {
				$suite->addTestFile($path . $file);
			}
		}
		return $suite;
	}

}
