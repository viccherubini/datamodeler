<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

require_once 'Type/AllTests.php';

require_once 'IteratorTest.php';
require_once 'LibTest.php';
require_once 'MiscTest.php';
require_once 'ModelTest.php';
require_once 'SqlTest.php';
require_once 'SqlResultTest.php';
require_once 'TypeTest.php';

class AllTests {
	public static function suite() {
		$suite = new \PHPUnit_Framework_TestSuite('DataModeler Tests');

		$suite->addTestSuite(\DataModelerTest\Type\AllTests::suite());
		
		$suite->addTestSuite('\DataModelerTest\IteratorTest');
		$suite->addTestSuite('\DataModelerTest\LibTest');
		$suite->addTestSuite('\DataModelerTest\MiscTest');
		$suite->addTestSuite('\DataModelerTest\ModelTest');
		$suite->addTestSuite('\DataModelerTest\SqlTest');
		$suite->addTestSuite('\DataModelerTest\SqlResultTest');
		$suite->addTestSuite('\DataModelerTest\TypeTest');
		
		return $suite;
	}
}