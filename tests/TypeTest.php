<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use \DataModeler\Type;

class TypeTest extends TestCase {

	public function testSetFieldName_ReturnsSelf() {
		$abstractType = $this->getMockForAbstractClass('\DataModeler\Type');
		
		$this->assertTrue($abstractType->setFieldName('field_name') instanceof \DataModeler\Type);
	}
	
	public function testGetFieldName_ReturnsTheFieldName() {
		$fieldName = 'field_name';
		
		$abstractType = $this->getMockForAbstractClass('\DataModeler\Type');
		$abstractType->setFieldName($fieldName);
		
		$this->assertEquals($fieldName, $abstractType->getFieldName());
	}
	
	public function testSetMaxlength_MustBeInteger() {
		$maxlength = 'somestring';
		
		$abstractType = $this->getMockForAbstractClass('\DataModeler\Type');
		$abstractType->setMaxlength($maxlength);
		
		$this->assertEquals(0, $abstractType->getMaxlength());
	}
	
	public function testGetMaxlength_ReturnsTheMaxlength() {
		$maxlength = 10;
		
		$abstractType = $this->getMockForAbstractClass('\DataModeler\Type');
		$abstractType->setMaxlength($maxlength);
		
		$this->assertEquals($maxlength, $abstractType->getMaxlength());
	}
}