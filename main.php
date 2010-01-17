<?php

require_once 'DataModelerException.php';

require_once 'DataObject.php';
require_once 'DataModel.php';
require_once 'DataIterator.php';
require_once 'DataAdapterPdo.php';

class ProductObject extends DataObject {
	
}

class ProductModel extends DataModel {
	
}

try {
	$db_config = array(
		'server' => 'localhost',
		'username' => 'root',
		'password' => 'dba89da',
		'database' => 'ioncart'
	);
	
	$data_adapter = new DataAdapterPdo($db_config);
	$data_adapter->setDriver(DataAdapterPdo::DRIVER_MYSQL)->connect();
	
	$product = new ProductObject();
	$product_model = new ProductModel($data_adapter);


} catch ( DataModelerException $e ) {
	exit($e . PHP_EOL);
}