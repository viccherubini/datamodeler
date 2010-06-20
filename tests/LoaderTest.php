<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use \DataModeler\Loader;

require_once 'lib/Loader.php';

class LoaderTest extends TestCase {


	public function setUp() {
		
	}
	
	public function tearDown() {
		
	}
	
	public function testAttachAdapter_CanAttachMultipleAdapters() {
		$loader = new Loader;
		
		$adapter1 = $this->buildMockAdapter();
		$adapter2 = $this->buildMockAdapter();
		
		$loader->attachAdapter($adapter1);
		$loader->attachAdapter($adapter2);
		
		$this->assertEquals(2, count($loader->getAdapterList()));
	}
	
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testLoad_RequiresAtLeastOneAdapter() {
		$loader = new Loader();
		$product = $this->buildMockProduct();
		$product->id(1);
		
		$loader->load($product);
	}
}