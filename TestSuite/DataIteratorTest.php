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

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testIteratorMustBeArray() {
		$di = new DataIterator('foobar');
	}
	
	public function testIteratorIsInitiallyAnEmptyArray() {
		$di = new DataIterator(array());
		$this->assertEquals($di->getDataList(), array());
		$this->assertEquals($di->length(), 0);
	}
	
	public function testIteratorLength() {
		$di = new DataIterator($this->data_list);
		$length = count($this->data_list);
		$this->assertEquals($di->length(), $length);
	}
	
	public function testFetchWithoutFilters() {
		$di1 = new DataIterator($this->data_list);
		$di2 = $di1->fetch();
		
		$this->assertEquals($di1->length(), $di2->length());
		$this->assertEquals($di1, $di2);
	}
	
	public function testFetchWithSingleFilter() {
		$first = reset($this->data_list);
		
		$di = new DataIterator($this->data_list);
		$di = $di->filter('name = ?', $first->getName())->fetch();
		
		$this->assertEquals($di->length(), 1);
		
		foreach ( $di as $item ) {
			$this->assertEquals($first->getName(), $item->getName());
		}
	}
	
	public function testFetchWithMultipleFilters() {
		$di = new DataIterator($this->data_list);
		$di = $di->filter('favorite_number = ?', 10)->filter('age = ?', 48)->fetch();
		
		$this->assertLessThan($di->length(), 0);
		
		foreach ( $di as $item ) {
			$this->assertEquals(10, $item->getFavoriteNumber());
			$this->assertEquals(48, $item->getAge());
		}
	}
	
	public function testDifferentComparisonFilters() {
		// Each of these should return 1 or more items, so we need to check the length of them all too.
		
		$test_age = 48;
		$di_pristine = new DataIterator($this->data_list);
		
		$di_equals = $di_pristine->filter('age == ?', $test_age)->fetch();
		
		$this->assertLessThan($di_equals->length(), 0);
		
		foreach ( $di_equals as $item ) {
			$this->assertEquals($test_age, $item->getAge());
		}
		
		$di_not_equal1 = $di_pristine->filter('age   <>  ?', $test_age)->fetch();
		$di_not_equal2 = $di_pristine->filter('age!=?', $test_age)->fetch();
		
		$this->assertLessThan($di_not_equal1->length(), 0);
		$this->assertLessThan($di_not_equal2->length(), 0);
		
		foreach ( $di_not_equal1 as $item ) {
			$this->assertTrue($test_age != $item->getAge());
		}
		
		foreach ( $di_not_equal2 as $item ) {
			$this->assertTrue($test_age != $item->getAge());
		}
		
		$di_gt = $di_pristine->filter('age>?', $test_age)->fetch();
		$di_gte = $di_pristine->filter('age >= ?', $test_age)->fetch();
		$di_lt = $di_pristine->filter('age < ?', $test_age)->fetch();
		$di_lte = $di_pristine->filter('age<=     ?', $test_age)->fetch();
		
		$this->assertLessThan($di_gt->length(), 0);
		$this->assertLessThan($di_gte->length(), 0);
		$this->assertLessThan($di_lt->length(), 0);
		$this->assertLessThan($di_lte->length(), 0);
		
		foreach ( $di_gt as $item ) {
			$this->assertGreaterThan($test_age, $item->getAge());
		}
		
		foreach ( $di_gte as $item ) {
			$this->assertGreaterThanOrEqual($test_age, $item->getAge());
		}
		
		foreach ( $di_lt as $item ) {
			$this->assertLessThan($test_age, $item->getAge());
		}
		
		foreach ( $di_lte as $item ) {
			$this->assertLessThanOrEqual($test_age, $item->getAge());
		}
	}
	
	public function testPagination() {
		$data = range(1, 100);
		
		$di = new DataIterator($data);
		
		$limit = $i = 10;
		$page = 2;
		$di->limit($limit)->page($page);
		
		foreach ( $di as $item ) {
			$this->assertEquals($item, $data[$i++]);
		}
		
		$this->assertEquals($di->length(), 20);
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