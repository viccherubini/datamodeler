<?php

require_once 'DataModelerException.php';

require_once 'DataObject.php';
require_once 'DataModel.php';
require_once 'DataQueryier.php';
require_once 'DataIterator.php';
require_once 'DataAdapterPdoMysql.php';

class Product extends DataObject {
}


try {

	$product = new Product();
	$product->setName('here is the product name');
	
	
	
	
} catch ( DataModelerException $e ) {
	exit($e . PHP_EOL);
}