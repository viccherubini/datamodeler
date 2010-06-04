<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

require_once 'lib/Adapter/Adapter.php';

class AdapterTest extends TestCase {

	public function testAdapterIdIsSetOnCreation() {
		$adapter = $this->buildMockAdapter();
		
		$adapter_id = $adapter->getId();
		$this->assertFalse(empty($adapter_id));
	}

}