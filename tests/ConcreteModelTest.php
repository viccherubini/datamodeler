<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use \DataModeler\Model, \DataModeler\Type, \DataModeler\Type\String;

require_once 'lib/Model.php';

class Order extends Model {
	
	/** [type DATETIME] */
	private $date_created = NULL;
	
	/** [type DATETIME] */
	private $date_updated = NULL;
	
	/** [type DATE] */
	private $date_available = NULL;
	
	/** [type INTEGER] */
	private $customer_id = 0;
	
	/** [type FLOAT] [precision 2] */
	private $price = 0.00;
	
	/** [type STRING] [maxlength 64] */
	private $name = NULL;
}

class ConcreteModelTest extends TestCase {

	public function testConstructor_BuildsSchema() {
		$order = new Order();
	}

}