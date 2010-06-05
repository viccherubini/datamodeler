<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Adapter;

require_once 'PHPUnit/Framework.php';
require_once 'Adapter/SqlTest.php';

class AllTests {
	
	public static function suite() {
		$suite = new \PHPUnit_Framework_TestSuite('DataModeler Adapter Tests');
		
		$suite->addTestSuite('\DataModelerTest\Adapter\SqlTest');
		
		return $suite;
	}
}