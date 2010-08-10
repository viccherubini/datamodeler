<?php

declare(encoding='UTF-8');
namespace DataModelerTest\Type;

require_once 'PHPUnit/Framework.php';

require_once 'Type/BoolTypeTest.php';
require_once 'Type/DateTypeTest.php';
require_once 'Type/DatetimeTypeTest.php';
require_once 'Type/FloatTypeTest.php';
require_once 'Type/IntegerTypeTest.php';
require_once 'Type/StringTypeTest.php';
require_once 'Type/TextTypeTest.php';
require_once 'Type/TypelessTypeTest.php';

class AllTests {
	
	public static function suite() {
		$suite = new \PHPUnit_Framework_TestSuite('DataModeler Type Tests');
		
		$suite->addTestSuite('\DataModelerTest\Type\BoolTypeTest');
		$suite->addTestSuite('\DataModelerTest\Type\DateTypeTest');
		$suite->addTestSuite('\DataModelerTest\Type\DatetimeTypeTest');
		$suite->addTestSuite('\DataModelerTest\Type\FloatTypeTest');
		$suite->addTestSuite('\DataModelerTest\Type\IntegerTypeTest');
		$suite->addTestSuite('\DataModelerTest\Type\StringTypeTest');
		$suite->addTestSuite('\DataModelerTest\Type\TextTypeTest');
		$suite->addTestSuite('\DataModelerTest\Type\TypelessTypeTest');
		
		return $suite;
	}
}