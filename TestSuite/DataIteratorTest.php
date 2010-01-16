<?php

require_once 'PHPUnit/Framework.php';
require_once 'DataObject.php';
require_once 'DataIterator.php';

class DataIteratorTest extends PHPUnit_Framework_TestCase {

	protected $data_list = array();
	
	protected function setUp() {
		$this->data_list = array(
			$this->getMockDataObject('Vic Cherubini', 'vmc@leftnode.com', 25),
			$this->getMockDataObject('Bob Saget', 'bob@saget.com', 53),
			$this->getMockDataObject('Randy Newton', 'randy@newton.com', 18),
			$this->getMockDataObject('Jon Stewart', 'jon@stewart.com', 48),
			$this->getMockDataObject('Bob Goldweight', 'bob@goldweight.com', 48),
			$this->getMockDataObject('Jeremy Jones', 'jones@jeremy.com', 18),
			$this->getMockDataObject('Rod Stiffington', 'rod@stiffington.com', 48),
			$this->getMockDataObject('Jack Mayoffer', 'jack@mayoffer.com', 32),
			$this->getMockDataObject('Mike Hunt', 'mike@hunt.com', 65),
			$this->getMockDataObject('Poor Fellow', 'poor@fellow.com', 48),
		);
	}
	
	public function testIteratorMustBeArray() {
		try {
			$di = new DataIterator('abc', $this->getMockDataObject());
		} catch ( Exception $e ) {
			$this->assertNull($di);
			return true;
		}
		
		$this->fail();
	}
	
	public function testIteratorIsInitiallyAnEmptyArray() {
		$di = new DataIterator(array(), $this->getMockDataObject());
		$this->assertEquals($di->getList(), array());
	}
	
	public function testIteratorLength() {
		$di = new DataIterator($this->data_list, $this->getMockDataObject());
		$length = count($this->data_list);
		$this->assertEquals($di->length(), $length);
	}
	
	public function testFetchWithoutFilters() {
		$di1 = new DataIterator($this->data_list, $this->getMockDataObject());
		$di2 = $di1->fetch();
		$this->assertEquals($di1, $di2);
	}
	
	public function testFetchWithSingleFilter() {
		$mock = $this->getMockDataObject();
		$first = reset($this->data_list);
		$di1 = new DataIterator($this->data_list, $mock);
		$di2 = new DataIterator(array($first), $mock);
		
		$di3 = $di1->filter('name = ?', $first->getName())->fetch();
		
		$this->assertEquals($di2, $di3);
		$this->assertEquals($di2->length(), $di3->length());
	}
	
	public function testFetchWithMultipleFilters() {
		$mock = $this->getMockDataObject();
		
		/* Not sure if this is the best way to do things. */
		$manually_found_result_list = array();
		foreach ( $this->data_list as $list_item ) {
			if ( 10 == $list_item->getFavoriteNumber() && 48 == $list_item->getAge() ) {
				$manually_found_result_list[] = $list_item;
			}
		}
		
		$di1 = new DataIterator($this->data_list, $mock);
		$di2 = new DataIterator($manually_found_result_list, $mock);
		
		$di3 = $di1->filter('favorite_number = ?', 10)->filter('age = ?', 48)->fetch();
		
		$this->assertEquals($di2, $di3);
		$this->assertEquals($di2->length(), $di3->length());
	}
	
	protected function getMockDataObject($name=NULL, $email=NULL, $age=18) {
		$data_object = $this->getMockForAbstractClass('DataObject');
		$data_object->setName($name);
		$data_object->setEmail($email);
		$data_object->setAge($age);
		$data_object->setFavoriteNumber(10);
		
		return $data_object;
	}
}