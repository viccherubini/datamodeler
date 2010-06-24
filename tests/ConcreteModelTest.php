<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use \DataModeler\Model;

require_once 'lib/Model.php';

class Product extends Model {
	
	/** [type STRING] [maxlength 64] */
	private $field_name = NULL;
	
	/** [type INTEGER] [maxlength 2] */
	private $age = NULL;
	
	/** [type INTEGSTER] [maxlength 2] */
	private $height = NULL;
}

class ConcreteModelTest extends TestCase {

	public function testConstructor_BuildsSchema() {
		$product = new Product();
		
		print_r($product->schema());
	}

}