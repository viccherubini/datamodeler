<?php

require_once 'DataModelerException.php';

require_once 'DataObject.php';
require_once 'DataModel.php';
require_once 'DataQueryier.php';
require_once 'DataIterator.php';
require_once 'DataAdapterPdoMysql.php';

class ProductObject extends DataObject {
}

class ProductModel extends DataModel {
	protected function init() {
		$this->setTable('product');
		$this->setPkey('product_id');
	}
}

try {
	

	$data_adapter = new DataAdapterPdoMysql('localhost', 'data_modeler', 'root', 'dba89da');
	$data_adapter->connect();
	
	$product = new ProductObject();
	$product_model = new ProductModel($data_adapter);


	$data_queryier = new DataQueryier($product_model);
	
	$iterator = $data_queryier->where('product_id <> ?', 3)->find($product);

	$obj = $iterator->current();
	
	

} catch ( DataModelerException $e ) {
	exit($e . PHP_EOL);
}