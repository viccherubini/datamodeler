<?php

require_once 'configure.php';

require_once 'DataModelerException.php';

require_once 'DataObject.php';
require_once 'DataModel.php';
require_once 'DataQueryier.php';
require_once 'DataIterator.php';
require_once 'DataAdapterPdoMysql.php';

class Product extends DataObject {
}


try {

	$db = new DataAdapterPdoMysql($db_hostname, $db_database, $db_username, $db_password);
	$db->connect();

	$product = new Product();
	$model = new DataModel($db);
	
	//$product = $model->load($product, 1);
	$product->setName('brand new product')->setPrice(1856)->setDateCreate(560500);
	$id = $model->save($product);
	
	echo $id . PHP_EOL;
} catch ( DataModelerException $e ) {
	exit($e . PHP_EOL);
}