<?php

require_once 'PHPUnit/Framework.php';
require_once 'DataObject.php';

class DataObjectConcrete extends DataObject {
	protected $special_type = NULL;
	
	const SPECIAL_TYPE_ONE = 'AB';
	const SPECIAL_TYPE_TWO = 'CD';
	
	public function setSpecialType($type) {
		if ( $type != self::SPECIAL_TYPE_ONE && $type != self::SPECIAL_TYPE_TWO ) {
			throw new Exception('Type can only be SPECIAL_TYPE_ONE or SPECIAL_TYPE_TWO');
		}
		
		$this->special_type = $type;
	}
	
	public function getSpecialType() {
		return $this->special_type;
	}
}

class DataObjectTest extends PHPUnit_Framework_TestCase {
	protected $doc = NULL;
	
	protected function setUp() {
		$this->doc = new DataObjectConcrete();
	}

	public function testBasicGetterSetter() {
		$this->doc->setPrice(10.99);
		$this->assertEquals($this->doc->getPrice(), 10.99);
		
		$this->doc->setName('Some very long name here.');
		$this->assertEquals($this->doc->getName(), 'Some very long name here.');
		
		$this->assertNull($this->doc->getDoesntExist());
	}
	
	public function testMagicCallMethod() {
		$this->doc->setPrice(1099);
		$this->assertEquals($this->doc->getPrice(), 1099);
		
		$this->doc->setPrice(1793);
		$this->assertEquals($this->doc->getPrice(), 1793);
	}
	
	public function testMethodCache() {
		$this->doc->setVeryLongMethodName('some value');
		$this->assertEquals($this->doc->getVeryLongMethodName(), 'some value');
		
		$this->doc->setVeryLongMethodName('another value');
		$this->assertEquals($this->doc->getVeryLongMethodName(), 'another value');
	}
	
	public function testSetSpecialTypes() {
		try {
			$this->doc->setSpecialType('ABC');
		} catch ( Exception $e ) {
			$this->assertNull($this->doc->getSpecialType());
			return true;
		}
		
		$this->fail();
	}
	
	public function testSetSpecialTypes2() {
		try {
			$this->doc->setSpecialType(DataObjectConcrete::SPECIAL_TYPE_ONE);
		} catch ( Exception $e ) {
			$this->fail();
		}
		
		return true;
	}
}