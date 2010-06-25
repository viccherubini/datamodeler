<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Type;

require_once 'PHPUnit/Framework.php';
require_once 'Type/BoolTest.php';
require_once 'Type/DateTest.php';
require_once 'Type/DatetimeTest.php';
require_once 'Type/FloatTest.php';
require_once 'Type/IntegerTest.php';
require_once 'Type/StringTest.php';
require_once 'Type/TextTest.php';

class AllTests {
	
	public static function suite() {
		$suite = new \PHPUnit_Framework_TestSuite('DataModeler Type Tests');
		
		$suite->addTestSuite('\DataModelerTest\Type\BoolTest');
		$suite->addTestSuite('\DataModelerTest\Type\DateTest');
		$suite->addTestSuite('\DataModelerTest\Type\DatetimeTest');
		$suite->addTestSuite('\DataModelerTest\Type\FloatTest');
		$suite->addTestSuite('\DataModelerTest\Type\IntegerTest');
		$suite->addTestSuite('\DataModelerTest\Type\StringTest');
		$suite->addTestSuite('\DataModelerTest\Type\TextTest');
		
		return $suite;
	}
}