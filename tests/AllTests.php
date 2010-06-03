<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

require_once 'ModelTest.php';

class AllTests {
	public static function suite() {
		$suite = new \PHPUnit_Framework_TestSuite('DataModeler Tests');

		$suite->addTestSuite('\DataModelerTest\ModelTest');
		
		return $suite;
	}
}