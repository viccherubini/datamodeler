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
		
		$message = "{$class}_{$method}_{$exceptionMessage}";
		
		$this->assertEquals($message, $exception->getMessage());
	}
}