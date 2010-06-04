<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

require_once 'lib/Writer.php';

class WriterTest extends TestCase {

	public function testAdapterListIsInitiallyEmpty() {
		$writer = new \DataModeler\Writer;
		
		$this->assertEmptyArray($writer->getAdapterList());
	}

	public function testAdapterCanBeAttached() {
		$adapter = $this->buildMockAdapter();
		
		$writer = new \DataModeler\Writer;
		$writer->addAdapter($adapter);
		
		$this->assertEquals(1, count($writer->getAdapterList()));
	}

}