<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

require_once 'AdapterTest.php';
require_once 'ModelTest.php';
require_once 'WriterTest.php';

class AllTests {
	public static function suite() {
		$suite = new \PHPUnit_Framework_TestSuite('DataModeler Tests');

		$suite->addTestSuite('\DataModelerTest\AdapterTest');
		$suite->addTestSuite('\DataModelerTest\ModelTest');
		$suite->addTestSuite('\DataModelerTest\WriterTest');
		
		return $suite;
	}
}