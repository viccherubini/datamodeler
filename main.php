<?php

require_once 'configure.php';

require_once 'DataModelerException.php';

require_once 'DataObject.php';
require_once 'DataModel.php';
require_once 'DataIterator.php';
require_once 'DataAdapterPdo.php';

class Product extends DataObject {
}


try {
	$product = new Product();
	
	$dsn = "mysql:host={$db_hostname};port=3306;dbname={$db_database}";
	$pdo = new PDO($dsn, $db_username, $db_password);
	$db = new DataAdapterPdo($pdo);
	$model = new DataModel($db);
	
	/* Load the first matched record. Return it to $product. */
	//$matched_product = $model->where('product_id = ?', 1)->loadFirst($product);
	//echo $matched_product->getName() . PHP_EOL;
	
	$product->setProductId(5)->setName('ddd-my new product')->setPrice(8895);
	$model->save($product);
	
	/* Load all matched products into an iterator. Each element of the iterator is a Product > DataObject object. */
	/*$iterator = $model->field('product_id', 'name', 'price')
		->where('product_id != ?', 4)
		->where('name != ?', 'Second Product')
		->orderBy('name', 'DESC')
		->groupBy('name')
		->limit(2)
		->loadAll($product);
	
	foreach ( $iterator as $obj ) {
		echo $obj->getName() . PHP_EOL;
	}*/

} catch ( PDOException $e ) {
	exit($e->getMessage() . PHP_EOL);
} catch ( DataModelerException $e ) {
	exit($e->getMessage() . PHP_EOL);
}