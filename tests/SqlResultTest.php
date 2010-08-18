<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use \DataModelerTest\TestCase,
	\DataModeler\SqlResult;

require_once 'DataModeler/Model.php';
require_once 'DataModeler/SqlResult.php';

class SqlResultTest extends TestCase {
	
	
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testAttachModel_MustBeModelObject() {
		$sqlResult = new SqlResult;
		$sqlResult->attachModel(NULL);
	}
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testAttachStatement_MustBeModelObject() {
		$sqlResult = new SqlResult;
		$sqlResult->attachStatement(NULL);
	}
	
}