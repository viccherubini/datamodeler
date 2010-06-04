# DataModeler
DataModeler is a new ORM type framework for bulding Models that can easily speak to any datastore, mainly because they do not actually speak to the data store themselves. The `\DataModeler\Model` class is designed in such a way that it is data store agnostic. You can save it in a SQL database, a NoSQL database, or even a caching system like memcache or APC.

This all occurs because, again, the Models are essentially blind as to where they are stored. It is up to each data store adapter to determine how to store that model. Models, however, should be intelligent in that they contain most of the logic your application requires. Remember, you want **F A T** models and skinny controllers.

Using DataModeler is simple. All of your models should extend the `\DataModeler\Model` class (which is fairly simple itself). Next, you will build a `\DataModeler\Loader` and `\DataModeler\Writer` object for loading models and writing them, respectively. A `\DataModeler\Adapater` instance is then built from an Abstract Factory class. Finally, the adapter is connected to through a consistent interface and attached to both the Loader and Writer objects. You can now load and write objects.

Of course, the order is not important initially; the Adapter can be built before the Loader and Writer objects. However, to load and write objects, an Adapater much be attached to each of the Loader and Writer.

You can attach multiple adapters to a single Writer. Each time `write()` is called on the Writer object, the Model sent to `write()` will be written to each attached Adapter.

## Sample Writing Application
	<?php
	
	declare(encoding='UTF-8');
	
	use DataModeler, DataModeler\Adapter, DataModeler\Adapter\Document;
	
	require_once 'lib/Model.php';
	require_once 'lib/Writer.php';
	require_once 'lib/Adapter/Pdo.php';
	require_once 'lib/Adapter/Document/Mongodb.php';
	
	class \Product extends Model {
	}
	
	$pdo_adapter = new Pdo;
	$pdo_adapter->setDriver('mysql')
		->setServer('localhost')
		->setUsername('username')
		->setPassword('password')
		->setDatabase('products');
	$pdo_adapter->connect();
	
	$mongodb_adapter = new Mongodb;
	$mongodb_adapter->setServer('localhost')
		->setUsername('username')
		->setPassword('password')
		->setDatabase('mongodb');
	
	$writer = new Writer();
	$writer->addAdapter($pdo_adapter);
	$writer->addAdapter($mongodb_adapter);
	
	
	$product = new \Product;
	$product->setName('Writing Advance PHP Applications: A Book')
		->setPrice(28.94)
		->setSku('WAPA');
		
	$writer->write($product);
	