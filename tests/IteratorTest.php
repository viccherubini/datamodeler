<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use DataModeler\Iterator, DataModeler\Model;

require_once 'DataModeler/Iterator.php';
require_once DIRECTORY_MODELS . 'Product.php';

class IteratorTest extends TestCase {

	private $iteratorArrayData = array();
	private $iteratorModelData = array();
	
	public function setUp() {
		$this->iteratorArrayData = array(
			array('name' => 'Product 1', 'price' => 18.88),
			array('name' => 'Product 2', 'price' => 44.92),
			array('name' => 'Product 3', 'price' => 85.73),
			array('name' => 'Product 4', 'price' => 88.90)
		);
		
		$modelList = array();
		foreach ( $this->iteratorArrayData as $arrayData ) {
			$product = new Product;
			foreach ( $arrayData as $k => $v ) {
				$product->$k = $v;
			}
			$modelList[] = $product;
		}
		
		$this->iteratorModelData = $modelList;
	}
	
	public function test__Clone_NewIteratorContainsSameData() {
		$iterator1 = new Iterator($this->iteratorArrayData);
		$iterator2 = clone $iterator1;
		
		$this->assertEquals($iterator1->getData(), $iterator2->getData());
	}

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
		
		$matchedIterator = $iterator->fetch();
		
		$this->assertEquals($iterator->getData(), $matchedIterator->getData());
	}
	
	
	/**
	 * @dataProvider providerFilter
	 */
	public function testFetch_ReturnsMatchedIteratorForArrays($field, $value, $expected) {
		$iterator = new Iterator($this->iteratorArrayData);
		
		$iterator->filter($field, $value);
		$matchedIterator = $iterator->fetch();
		
		$this->assertEquals($expected, $matchedIterator->getData());
	}
	
	
	/**
	 * @dataProvider providerFilterWithLimit
	 */
	public function testFetch_ReturnsMatchedIteratorForArraysWithMaxLimit($field, $value, $expected, $limit) {
		$iterator = new Iterator($this->iteratorArrayData);
		
		$iterator->filter($field, $value);
		$iterator->limit($limit);
		
		$matchedIterator = $iterator->fetch();
		
		$this->assertEquals($expected, $matchedIterator->getData());
	}

	/**
	 * @dataProvider providerFilter
	 */
	public function testFetch_CanFilterOnAnArrayOfModels($field, $value, $expected) {
		$iterator = new Iterator($this->iteratorModelData);
		
		$iterator->filter($field, $value);
		$matchedIterator = $iterator->fetch();
		
		$this->assertEquals(count($expected), $matchedIterator->length());
	}

	public function providerIteratorArray() {
		return array(
			array(array(1, 2, 3, 4, 5)),
			array(array('a', 'b', 'c', 'd', 'e')),
			array(array(array(1), array(2), array(3), array(4), array(5), array(6))),
			array(array(new \stdClass, new \stdClass, new \stdClass))
		);
	}
	
	public function providerFilter() {
		return array(
			array('name = ?', 'Product 1', array(array('name' => 'Product 1', 'price' => 18.88))),
			array('name == ?', 'Product 1', array(array('name' => 'Product 1', 'price' => 18.88))),
			array('price != ?', 18.88, array(array('name' => 'Product 2', 'price' => 44.92), array('name' => 'Product 3', 'price' => 85.73), array('name' => 'Product 4', 'price' => 88.90))),
			array('price <> ?', 18.88, array(array('name' => 'Product 2', 'price' => 44.92), array('name' => 'Product 3', 'price' => 85.73), array('name' => 'Product 4', 'price' => 88.90))),
			array('price >= ?', 85.73, array(array('name' => 'Product 3', 'price' => 85.73), array('name' => 'Product 4', 'price' => 88.90))),
			array('price <= ?', 44.92, array(array('name' => 'Product 1', 'price' => 18.88), array('name' => 'Product 2', 'price' => 44.92))),
			array('price < ?', 44.92, array(array('name' => 'Product 1', 'price' => 18.88))),
			array('price > ?', 85.73, array(array('name' => 'Product 4', 'price' => 88.90)))
		);
	}
	
	public function providerFilterWithLimit() {
		return array_map(function($e) {
			$e[2] = array($e[2][0]);
			$e[] = 1;
			return $e;
		}, $this->providerFilter());
	}
	
}