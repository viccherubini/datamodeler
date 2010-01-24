<?php

require_once 'configure.php';

require_once 'DataModelerException.php';

require_once 'DataObject.php';
require_once 'DataModel.php';
require_once 'DataIterator.php';
require_once 'DataAdapterPdoMysql.php';

class Product extends DataObject {
}


try {

	$db = new DataAdapterPdoMysql($db_hostname, $db_database, $db_username, $db_password);
	$db->connect();

	$product = new Product();
	$model = new DataModel($db);
	
	//$product = $model->where('product_id = ?', 1)->loadFirst($product);
	//echo $product->getName() . PHP_EOL;
	
	$iterator = $model->field('product_id', 'name', 'price')->where('product_id != ?', 4)->where('name != ?', 'Second Product')->orderBy('name', 'DESC')->groupBy('name')->limit(2)->loadAll($product);
	
	if ( true ) {
	foreach ( $iterator as $obj ) {
		echo $obj->getName() . PHP_EOL;
	}
	}
	
} catch ( DataModelerException $e ) {
	exit($e . PHP_EOL);
}