<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use \DataModeler\Model;

require_once 'lib/Model.php';

class Product extends Model {
	protected $table = 'products';
	
	protected $pkey = 'product_id';
	
	/** [type INTEGER] */
	private $product_id = 0;
	
	/** [type DATETIME] */
	private $date_created = NULL;
	
	/** [type DATETIME] [default NULL] */
	private $date_updated = NULL;
	
	/** [type DATE] */
	private $date_available = NULL;
	
	/** [type INTEGER] */
	private $customer_id = 0;
	
	/** [type FLOAT] [precision 2] */
	private $price = 0.00;
	
	/** [type STRING] [maxlength 64] */
	private $name = NULL;
	
	/** [type STRING] [maxlength 12] [default SKUP1] */
	private $sku;
	
	private $field = NULL;
}