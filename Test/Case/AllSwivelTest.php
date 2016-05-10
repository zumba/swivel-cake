<?php
/**
 * All Swivel plugin tests
 */
class AllSwivelTest extends CakeTestCase {

/**
 * Suite define the tests for this plugin
 *
 * @return void
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Swivel test');

		$path = CakePlugin::path('Swivel') . 'Test' . DS . 'Case' . DS;
		$suite->addTestDirectoryRecursive($path);

		return $suite;
	}

}
