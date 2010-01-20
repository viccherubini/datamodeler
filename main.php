<?php

require_once 'DataModelerException.php';

require_once 'DataObject.php';
require_once 'DataModel.php';
require_once 'DataRelationship.php';
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
	$product_model = new ProductModel($data_adapter, 'product');


	$data_relationship = new DataRelationship($product_model);
	
	$iterator = $data_relationship->where('product_id = ?', 3)->where('name <> ?', 'some new name')->find($product);

	foreach ( $iterator as $obj ) {
		print_r($obj);
	}

	//$product_model->load($product, 3);
	//$product->setName('new name');
	//$product_model->save($product);
	
} catch ( DataModelerException $e ) {
	exit($e . PHP_EOL);
}