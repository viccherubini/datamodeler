<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

require_once 'lib/Adapter.php';

class AdapterTest extends TestCase {

	public function testGetId_IsSetOnAdapterCreation() {
		$adapter = $this->buildMockAdapter();
		
		$adapter_id = $adapter->getId();
		$this->assertFalse(empty($adapter_id));
	}


	public function testGetPriority_IsCastToInteger() {
		$adapter = $this->buildMockAdapter();
		
		$adapter->setPriority('some string');
		$this->assertTrue(is_int($adapter->getPriority()));
	}
}