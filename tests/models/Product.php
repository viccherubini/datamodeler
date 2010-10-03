<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use \DataModeler\Model;

require_once 'DataModeler/Model.php';

class Product extends Model {
	
	protected $table = 'products';
	protected $pkey = 'product_id';
	
	/** [type INTEGER] */
	public $product_id = 0;
	
	/** [type DATETIME] */
	public $date_created = self::SCHEMA_TYPE_DATETIME_VALUE;
	
	/** [type DATETIME] */
	public $date_updated = NULL;
	
	/** [type DATE] */
	public $date_available = self::SCHEMA_TYPE_DATE_VALUE;
	
	/** [type STRING] [maxlength 64] */
	public $name = NULL;
	
	/** [type FLOAT] [precision 2] */
	public $price = 0.99;
	
	/** [type STRING] [maxlength 12] */
	public $sku = 'SKU1';
	
	/** [type TEXT] */
	public $description = NULL;
	
	/** [type STRING] [maxlength 128] */
	public $image = NULL;
	
	/** [type BOOL] */
	public $available = false;
	
	public $store_name = NULL;
	
	/** [type INTEGER] */
	public $uses = 0;
}