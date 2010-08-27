<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use \DataModeler\Exception;

class ExceptionTest extends TestCase {
	
	public function test__Construct_IncludesClassAndMethod() {
		$exceptionMessage = 'error';
		$exception = new Exception($exceptionMessage);
		
		$class = __CLASS__;
		$method = __FUNCTION__;
		$filename = __FILE__;
		$line = __LINE__ - 5;
		
		$message = "{$class}->{$method}() [{$exceptionMessage}] ({$filename} +{$line})";
		
		$this->assertEquals($message, $exception->getMessage());
	}
}