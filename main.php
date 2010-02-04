<?php

require_once 'configure.php';

require_once 'DataModelerException.php';

require_once 'DataObject.php';
require_once 'DataModel.php';
require_once 'DataIterator.php';
require_once 'DataAdapterPdo.php';

class Product extends DataObject {
}

class Product_Favorite extends DataObject {
	public function __construct() {
		parent::__construct();
		$this->hasDate(false);
	}
}

try {
	/* New DataObject for working with. */
	$product = new Product();
	$product_favorite = new Product_Favorite();
	
	/* Create the PDO connection outside of the data adapter. */
	$dsn = "mysql:host={$db_hostname};port=3306;dbname={$db_database}";
	$pdo = new PDO($dsn, $db_username, $db_password);

	/**
	 * The DataModel lets us communicate with the data store. The DataModel is responsible
	 * for loading/updating/inserting/deleting DataObjects.
	 */
	$db = new DataAdapterPdo($pdo);
	$model = new DataModel($db);
	
	/* This will insert a new DataObject. */
	$product->setName('My New Product')->setPrice(7984);
	$model->save($product);
	
	/**
	 * Cheaply load a product without doing a query and update it. Be careful doing it this way, not recommended.
	 * In this case, $product has the name from the previous call, and if product_id 5 doesn't exist
	 * a record that doesn't exist will attempt to be updated, resulting in nothing. 
	 * You should always use $model->loadFirst($product).
	 */
	$product->setProductId(5)->setPrice(7845);
	$model->save($product);
	
	/**
	 * Load up a new DataObject. This loads all of the data into the $product variable.
	 */
	$product = $model->where('product_id = ?', 1)->loadFirst($product);
	echo $product->getName() . PHP_EOL;
	
	/* Test doing a basic inner join. */
	$favorited_product_list = $model->innerJoin($product_favorite)
		->loadAll($product);
	foreach ( $favorited_product_list as $obj ) {
		echo get_class($obj) . PHP_EOL;
	}
	
	/* Load all matched products into an iterator. Each element of the iterator is a Product > DataObject object. */
	$iterator = $model->field('product_id', 'name', 'price')
		->where('product_id != ?', 4)
		->where('name != ?', 'Second Product')
		->orderBy('name', 'DESC')
		->groupBy('name')
		->limit(2)
		->loadAll($product);
	
	foreach ( $iterator as $obj ) {
		echo $obj->getName() . PHP_EOL;
	}

} catch ( PDOException $e ) {
	exit($e->getMessage() . PHP_EOL);
} catch ( DataModelerException $e ) {
	exit($e->getMessage() . PHP_EOL);
}