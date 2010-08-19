<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

require_once 'DataModeler/Lib.php';

class LibTest extends TestCase {

	public function testIsScalarArray_MustBeArray() {
		$this->assertFalse(\DataModeler\is_scalar_array(NULL));
		$this->assertFalse(\DataModeler\is_scalar_array('abc'));
		$this->assertFalse(\DataModeler\is_scalar_array(10.44));
		$this->assertFalse(\DataModeler\is_scalar_array(44));
		$this->assertFalse(\DataModeler\is_scalar_array(new \stdClass));
	}

	public function testIsScalarArray_ArrayMustBeNonEmpty() {
		$this->assertFalse(\DataModeler\is_scalar_array(array()));
	}
	
	public function testIsScalarArray_ElementsAreScalar() {
		$array = array(1, 2, '3', 'b' => 10, 34.33, 'string');
		$this->assertTrue(\DataModeler\is_scalar_array($array));
	}
	
	public function testIsScalarArray_ElementsAreNotScalar() {
		$array = array(1, 2, '3', 'b' => array('a', 'b', 'c'), 34.33, new \stdClass);
		$this->assertFalse(\DataModeler\is_scalar_array($array));
	}
	
	public function testObjectToArray_MustBeObject() {
		$this->assertEquals(0, count(\DataModeler\object_to_array(NULL)));
		$this->assertEquals(0, count(\DataModeler\object_to_array('abc')));
		$this->assertEquals(0, count(\DataModeler\object_to_array(10.44)));
		$this->assertEquals(0, count(\DataModeler\object_to_array(44)));
		$this->assertEquals(0, count(\DataModeler\object_to_array(array('a', 'b', 'c'))));
	}
	
	public function testObjectToArray_PullsPublicAttributes() {
		$object = new \stdClass;
		$object->a = 'a';
		$object->b = 'b';
		$object->c = 'c';
		
		$this->assertEquals(array('a' => 'a', 'b' => 'b', 'c' => 'c'), \DataModeler\object_to_array($object));
	}
}