# DataModeler
DataModeler is a new ORM type framework for bulding Models that can easily speak to any datastore, mainly because they do not actually speak to the data store themselves. The `\DataModeler\Model` class is designed in such a way that it is data store agnostic. You can save it in a SQL database, a NoSQL database, or even a caching system like memcache or APC.

This all occurs because, again, the Models are essentially blind as to where they are stored. It is up to each data store adapter to determine how to store that model. Models, however, should be intelligent in that they contain most of the logic your application requires. Remember, you want **F A T** models and skinny controllers.

Using DataModeler is simple. All of your models should extend the `\DataModeler\Model` class (which is fairly simple itself). Next, you'll define your Model's field's datatypes along with other Models they reference. This means you can define heirarchial Models and ensure your data is consistent with what is in your datastore.

Because of the complex nature of how Models can be loaded and written, you must define each Adapter you want to load from/save to. You'll most likely only use one Adapter, but adding a second to your application is simple.

## Sample Application
	<?php
	
	declare(encoding='UTF-8');
	
	use DataModeler\Model,
		DataModeler\Adapter\Sql,
		DataModeler\Adapter\Document\Redis;
	
	require_once 'lib/Model.php';
	require_once 'lib/Adapter/Sql.php';
	require_once 'lib/Adapter/Document/Redis.php';
	
	/**
	 * Automatically build an object that models data in a datastore.
	 */
	class \Product extends Model {
		
		/** [ref \Product\Image on pkey] */
		private $imageList = array();
		
		/** [ref \Product\Description on pid={product_id}] */
		private $description = NULL;
		
		/** [ref \Product\Icon on size=10] */
		private $iconList = array();


		/** [type INTEGER] */
		private $product_id = 0;

		/** [type STRING] [maxlength 64] */
		private $name = NULL;

		/** [type FLOAT] [precision 2] */
		private $price = 0.00;
		
		/** [type STRING] [maxlength 32] */
		private $sku;
	}
	
	/**
	 * Because the Sql class depends on an external object, the \PDO object,
	 * the object is constructed externally and added to the Sql object.
	 */
	$pdo = new \PDO('sqlite:/path/to/database.sqlite3');
	
	$sql = new Sql;
	$sql->attachDb($pdo);
	
	/**
	 * If an external \Redis object were ever necessary, it would be constructed
	 * externally and added to the Redis DataModeler object. For now, the
	 * management of a Redis connection will be handled internally since there's
	 * no dependency injection.
	 */
	$redis = new Redis;
	
	/**
	 * Create a new empty product and insert it into the database and Redis server.
	 */
	$product = new \Product;
	$product->setName('Using DataModeler In All Its Glory')
		->setPrice(13.99)
		->setSku('UDMIAIG');
		
	$sql->save($product);
	$redis->save($product);
	
	/**
	 * Load the product from the SQL datastore.
	 */
	$product = $sql->prepare($product)->get(1);
	
	/**
	 * Because of how $product is defined, when it is retrieved, several
	 * things will happen: all \Product\Image objects associated with that
	 * product will be loaded and stored into $imageList, a \Product\Description
	 * object will be loaded and stored in $description, and all \Product\Icon
	 * objects who have the size=10 will be loaded into $iconList.
	 */
	
	// $imageList is now an empty array or an array of \Product\Image objects
	$imageList = $product->getImageList();
	
	// $description is a \Product\Description object, even if nothing was found
	$description = $product->getDescription();


## Defining References
DataModeler is powerful because you can define references within a Model. A Model can refer to another Model or list of Models. Recursive loadings can not take place and you should build your databases so they're not required. When you define references, they'll be loaded when the parent Model is loaded, and saved when the parent Model is saved. By their nature, children Models are loaded recursively. As a result, you should be aware of the amount of queries required to load a large parent Model.

## Authors
Vic Cherubini <vmc@leftnode.com>