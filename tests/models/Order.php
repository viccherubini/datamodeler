<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use \DataModeler\Model;

require_once 'DataModeler/Model.php';

class Order extends Model {
	protected $table = 'orders';
	
	protected $pkey = 'order_id';
	
	/** [type INTEGER] */
	public $order_id = 0;
	
	/** [type DATETIME] */
	public $date_created = NULL;
	
	/** [type DATETIME] [default NULL] */
	public $date_updated = NULL;
	
	/** [type DATE] */
	public $date_available = NULL;
	
	/** [type INTEGER] */
	public $customer_id = 0;
	
	/** [type FLOAT] [precision 4]*/
	public $total = 0.0;
	
	/** [type STRING] [maxlength 64] */
	public $name = NULL;
}
