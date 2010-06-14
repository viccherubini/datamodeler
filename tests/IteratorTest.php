<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use DataModeler\Iterator;

require_once 'lib/Iterator.php';

class IteratorTest extends TestCase {

	/**
	 * @dataProvider providerIteratorArray
	 */
	public function testCurrent_ReturnsFirstElement($data) {
		$iterator = new Iterator($data);
		
		$this->assertEquals(current($data), $iterator->current());
	}
	
	
	/**
	 * @dataProvider providerIteratorArray
	 */
	public function testCurrent_ReturnsFirstElementWhenKeyOutOfBounds($data) {
		$iterator = new Iterator($data);
		
		// Obscure Test - Set the key way out of bounds of the size of the array
		$iterator->page(1000)
			->limit(10)
			->rewind();
	
		$this->assertEquals(current($data), $iterator->current());
	}
	
	/**
	 * @dataProvider providerIteratorArray
	 */
	public function testGetData_ReturnsAllData($data) {
		$iterator = new Iterator($data);
		
		$this->assertEquals($data, $iterator->getData());
	}
	
	
	/**
	 * @dataProvider providerIteratorArray
	 */
	public function testRewind_GoesToBeginningWhenNoPaginationSet($data) {
		$iterator = new Iterator($data);
		
		// Obscure Test - Reset everything to ensure current() returns first element.
		$iterator->rewind();
		
		$this->assertEquals(current($data), $iterator->current());
	}

	
	/**
	 * @dataProvider providerIteratorArray
	 */
	public function testLast_ReturnsLastElement($data) {
		$iterator = new Iterator($data);
		
		$this->assertEquals(end($data), $iterator->last());
	}
	
	
	public function testKey_ReturnsKeyNumber() {
		$iterator = new Iterator(array());
		
		$this->assertEquals(0, $iterator->key());
	}

	public function testKey_ReturnsKeyOffsetWithPageAndLimit() {
		$page = 10;
		$perPage = 10;
		$expectedKey = ($page - 1) * $perPage;
		
		$iterator = new Iterator(array());
		
		// Page 10, 10 Per Page
		$iterator->page($page)
			->limit($perPage)
			->rewind();
		
		$this->assertEquals($expectedKey, $iterator->key());
	}


	/**
	 * @dataProvider providerIteratorArray
	 */
	public function testNext_ReturnsNextElementRegardlessOfCurrentKey($data) {
		$iterator = new Iterator($data);
		
		// The value of $iterator->key() will be incorrect, but $iterator->next()
		// still works as expected.
		$iterator->page(10)
			->limit(10)
			->rewind();
		
		$this->assertEquals(next($data), $iterator->next());
		
	}
	
	
	/**
	 * @dataProvider providerIteratorArray
	 */
	public function testValid_IsInvalidForOutOfBoundKeys($data) {
		$iterator = new Iterator($data);
		
		$iterator->page(10)
			->limit(10)
			->rewind();
		
		$this->assertFalse($iterator->valid());
	}
	
	
	/**
	 * @dataProvider providerIteratorArray
	 */
	public function testValid_IsValidForInBoundKeys($data) {
		$iterator = new Iterator($data);
		
		$this->assertTrue($iterator->valid());
	}
	
	/**
	 * @dataProvider providerIteratorArray
	 */
	public function testValid_IsValidForKeyAtEndOfData($data) {
		$iterator = new Iterator($data);
		
		$iterator->page(count($data))->limit(1)->rewind();
		
		$this->assertTrue($iterator->valid());
		$this->assertEquals(end($data), $iterator->last());
	}
	
	
	public function testPage_CanOnlyBePositiveInteger() {
		$iterator = new Iterator(array());
		
		$iterator->page(-10);
		
		// PHP's awesome Reflection class comes to the rescue
		$iteratorPageProperty = new \ReflectionProperty($iterator, 'page');
		
		// Make 'page' public
		$iteratorPageProperty->setAccessible(true);
		
		$this->assertGreaterThanOrEqual(0, $iteratorPageProperty->getValue($iterator));
	}
	
	
	/**
	 * @dataProvider providerIteratorArray
	 */
	public function testLength_ReturnsDataLength($data) {
		$iterator = new Iterator($data);
		
		$this->assertEquals(count($data), $iterator->length());
	}
	
	
	public function testFilter_AddsNewFilter() {
		$iterator = new Iterator(array());
		
		$this->assertFalse($iterator->hasFilter());
		
		$iterator->filter('age > ?', 10);
		
		$this->assertTrue($iterator->hasFilter());
	}
	

	/**
	 * @dataProvider providerIteratorArray
	 */
	public function testFetch_ReturnsSameIteratorDataForNoFilters($data) {
		$iterator = new Iterator($data);
		
		$newIterator = $iterator->fetch();
		
		$this->assertEquals($iterator->getData(), $newIterator->getData());
	}

	public function providerIteratorArray() {
		return array(
			array(array(1, 2, 3, 4, 5)),
			array(array('a', 'b', 'c', 'd', 'e')),
			array(array(array(1), array(2), array(3), array(4), array(5), array(6))),
			array(array(new \stdClass, new \stdClass, new \stdClass))
		);
	}
	
	public function providerIteratorFilterableArray() {
		return array(
			array(array(array('name' => 'Vic Cherubini', 'age' => 18), array('name' => 'Bob Saget', 'age' => 44), array('name' => 'King George', 'age' => 85), array('name' => 'Rodney Dangerfield', 'age' => 88)))
		);
		
	}
}