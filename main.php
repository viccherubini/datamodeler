<?php

require_once 'DataModelerException.php';

require_once 'DataObject.php';
require_once 'DataModel.php';
require_once 'DataIterator.php';
require_once 'DataAdapterPdoMysql.php';

class ProductObject extends DataObject {
}

class ProductModel extends DataModel {
}

try {
	

	$data_adapter = new DataAdapterPdoMysql(DataAdapterPdo::DRIVER_MYSQL, 'localhost', 'data_modeler', 'root', 'dba89da');
	$data_adapter->connect();
	//$data_adapter->query('INSERT INTO `product` (`date_create`, `date_modify`, name, price) VALUES(?, ?, ?, ?)', array(time(), 0, "Jon's O'Reilly Honda", 1595));
	//echo $data_adapter->insertId();
	
	//$result = $data_adapter->query('SELECT * FROM `product` WHERE product_id = ?', array(1));
	//while ( $row = $result->fetch() ) {
	//	print_r($row);
	//}
	
	
	
	//$data_adapter->setDriver(DataAdapterPdo::DRIVER_MYSQL)->connect();
	$product = new ProductObject();
	$product_model = new ProductModel($data_adapter);


	$product->setName('new product')->setPrice(1895);
	$product_model->save($product);

} catch ( DataModelerException $e ) {
	exit($e . PHP_EOL);
}