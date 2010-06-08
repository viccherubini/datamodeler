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

}