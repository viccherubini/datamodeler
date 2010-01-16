<?php

require_once 'DataModelerException.php';

require_once 'DataObject.php';
require_once 'DataModel.php';
require_once 'DataIterator.php';
require_once 'DataAdapterAbstract.php';
require_once 'DataAdapterSqlite.php';

class ProductObject extends DataObject {
	
}

class ProductModel extends DataModel {
	
}

try {
	$product = new ProductObject();
	$product_model = new ProductModel();

	$product_model->save($product);
} catch ( DataModelerException $e ) {
	exit($e . PHP_EOL);
}