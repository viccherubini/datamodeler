<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use \DataModeler\Model;

require_once 'Order.php';
require_once 'Product.php';

class ModelTest extends TestCase {
	
	public function test__Call_ReturnsValueWithNoArguments() {
		$product = new Product;
		$dateCreated = $product->getDateCreated();
		$dateParsed = date_parse($dateCreated);
		
		$this->assertFalse(empty($dateCreated));
		$this->assertEquals(0, count($dateParsed['errors']));
	}
	
	public function test__Call_CanOnlyGetPredefinedField() {
		$product = new Product;
		$missingField = $product->getFieldThatDoesNotExist();
		
		$this->assertTrue(is_null($missingField));
	}
	
	public function test__Call_SetsValueWithArguments() {
		$productName = 'Product1';
		
		$product = new Product;
		$product->setName($productName);
		
		$this->assertEquals($productName, $product->getName());
	}
	
	/**
	 * @dataProvider providerNonScalarValue
	 */
	public function test__Call_CanNotSetScalarValue($value) {
		$product = new Product;
		$product->setName($value);
		$productName = $product->getName();
		
		$this->assertTrue(empty($productName));
	}
	
	/**
	 * @dataProvider providerScalarValue
	 */
	public function test__Call_CanOnlySetScalarValue($value) {
		$product = new Product;
		$product->setStoreName($value);
		
		$this->assertEquals($value, $product->getStoreName());
	}
	
	public function test__Call_CanOnlySetPredefinedField() {
		$fieldValue = 'Does Not Exist';
		
		$product = new Product;
		$product->setFieldThatDoesNotExist($fieldValue);
		
		$fieldThatDoesNotExist = $product->getFieldThatDoesNotExist();
		$this->assertTrue(empty($fieldThatDoesNotExist));
		$this->assertTrue(is_null($fieldThatDoesNotExist));
	}
	
	public function test__Call_SetsAllowChaining() {
		$productName = 'Product Name';
		$productPrice = 10.99;
		$productSku = 'SKU10_993';
		
		$product = new Product;
		$product->setName($productName)
			->setPrice($productPrice)
			->setSku($productSku);
		
		$this->assertEquals($productName, $product->getName());
		$this->assertEquals($productPrice, $product->getPrice());
		$this->assertEquals($productSku, $product->getSku());
	}
	
	public function testEqualTo_NewModelsAreEqual() {
		$product1 = new Product;
		$product2 = new Product;
		
		$this->assertTrue($product1->equalTo($product2));
		$this->assertTrue($product2->equalTo($product1));
	}

	public function testEqualTo_UpdatedModelsAreEqual() {
		$productName = 'Product';
		$productPrice = (mt_rand(1, 100) / mt_rand(5, 50));
		$productSku = 'PSKU1';
		
		$product1 = new Product;
		$product2 = new Product;
		
		$product1->setName($productName)
			->setPrice($productPrice)
			->setSku($productSku)
			->setDateCreated(Model::SCHEMA_TYPE_DATETIME_VALUE);
		
		$product2->setName($productName)
			->setPrice($productPrice)
			->setSku($productSku)
			->setDateCreated(Model::SCHEMA_TYPE_DATETIME_VALUE);
		
		$this->assertTrue($product1->equalTo($product2));
		$this->assertTrue($product2->equalTo($product1));
	}
	
	public function testEqualTo_NewDifferentModelsAreNotEqual() {
		$product = new Product;
		$order = new Order;
		
		$this->assertFalse($product->equalTo($order));
		$this->assertFalse($order->equalTo($product));
	}
	
	public function testEqualTo_UpdatedModelsAreNotEqual() {
		$product = new Product;
		$order = new Order;
		
		$product->setDateCreated(Model::SCHEMA_TYPE_DATETIME_VALUE);
		$order->setDateCreated(Model::SCHEMA_TYPE_DATETIME_VALUE);
		
		$this->assertFalse($product->equalTo($order));
		$this->assertFalse($order->equalTo($product));
	}
	
	public function testExists_IdIsNonempty() {
		$product = new Product;
		$product->id(10);
		
		$this->assertTrue($product->exists());
	}
	
	public function testExists_IdIsEmpty() {
		$product = new Product;
		
		$this->assertFalse($product->exists());
	}
	
	public function testExists_IdCanBeSetThroughLoad() {
		$product = new Product;
		$product->load(array('product_id' => 10));
		
		$this->assertTrue($product->exists());
	}

	public function testId_ReturnsId() {
		$productId = 10;
		$product = new Product;
		$product->id($productId);
		
		$this->assertEquals($productId, $product->id());
	}
	
	public function testId_CanSetIdAndGetNewId() {
		$productId = 10;
		$product = new Product;
		
		$this->assertEquals($productId, $product->id($productId));
	}
	
	public function testId_ChangingPkeyUpdatesId() {
		$productId = 10;
		$productPkey = 'pid';
		
		$product = new Product;
		$productPkeyOriginal = $product->pkey();
		$product->id($productId);
		$product->pkey($productPkey);
		
		$this->assertEquals($productId, $product->id());
		
		$product->pkey($productPkeyOriginal);
		$this->assertEquals($productId, $product->id());
	}
	
	public function testIsA_ModelNamesMustBeEqual() {
		$product1 = new Product;
		$product2 = new Product;
		
		$this->assertTrue($product1->isA($product2));
		$this->assertTrue($product2->isA($product1));
	}
	
	public function testIsA_ModelNamesAreNotEqual() {
		$product = new Product;
		$order = new Order;
		
		$this->assertFalse($product->isA($order));
		$this->assertFalse($order->isA($product));
	}
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testLoad_MustLoadArray() {
		$product = new Product;
		$product->load('string');
	}
	
	public function testLoad_CanOnlyLoadPredefinedFields() {
		$productData = array(
			'name' => 'Product Name',
			'price' => 10.99,
			'sku' => 'SKU1',
			'age' => 15
		);
		
		$product = new Product;
		$product->load($productData);
		
		$this->assertEquals($productData['name'], $product->getName());
		$this->assertEquals($productData['price'], $product->getPrice());
		$this->assertEquals($productData['sku'], $product->getSku());
		$this->assertTrue(is_null($product->getAge()));
	}
	
	public function testModel_ReturnsNvpArray() {
		$productData = array(
			'product_id' => 10,
			'name' => 'Product Name',
			'price' => 10.99,
			'sku' => 'SKU1'
		);
		
		$product = new Product;
		$product->load($productData);
		$model = $product->model();
		
		$this->assertEquals($productData['product_id'], $product->id());
		$this->assertEquals($productData['name'], $model['name']);
		$this->assertEquals($productData['price'], $model['price']);
		$this->assertEquals($productData['sku'], $model['sku']);
	}
	
	public function testModel_FieldsAlwaysSorted() {
		$product = new Product;
		$model = $product->model();
		
		$modelFieldsSorted = $modelFields = array_keys($model);
		sort($modelFieldsSorted);
		
		$this->assertEquals($modelFields, $modelFieldsSorted);
	}
	
	public function testModelId_IsHashOfClass() {
		$product = new Product;
		$productHash = sha1(get_class($product));
		
		$this->assertEquals($productHash, $product->modelId());
	}
	
	public function testModelId_IsEqualForSameClasses() {
		$product1 = new Product;
		$product2 = new Product;
		
		$this->assertEquals($product1->modelId(), $product2->modelId());
	}

	public function testModelMeta_FieldsAlwaysSorted() {
		$product = new Product;
		$modelMeta = $product->modelMeta();
		
		$modelFieldsSorted = $modelFields = array_keys($modelMeta);
		sort($modelFieldsSorted);
		
		$this->assertEquals($modelFields, $modelFieldsSorted);
	}
	
	public function testMultibyte_MustBeBool() {
		$product = new Product;
		
		$product->multibyte(true);
		$this->assertTrue($product->multibyte());
		
		$product->multibyte(false);
		$this->assertFalse($product->multibyte());
		
		$product->multibyte('not-bool');
		$this->assertTrue($product->multibyte());
		
		$product->multibyte(-1);
		$this->assertTrue($product->multibyte()); // Last value of Model::$multibyte
	}
	
	public function testPkey_ReturnsPkey() {
		$product = new Product;
		$productPkeyReflection = new \ReflectionProperty(get_class($product), 'pkey');
		$productPkeyReflection->setAccessible(true);
		$productPkey = $productPkeyReflection->getValue($product);
		
		$this->assertEquals($productPkey, $product->pkey());
	}
	
	public function testPkey_CanSetPkey() {
		$productPkey = 'pid';
		
		$product = new Product;
		$product->pkey($productPkey);
		
		$this->assertEquals($productPkey, $product->pkey());
	}
	
	public function testPkey_CanSetPkeyAndGetNewPkey() {
		$productPkey = 'pid';
		
		$product = new Product;
		
		$this->assertEquals($productPkey, $product->pkey($productPkey));
	}
	
	public function testPkey_UpdatesModel() {
		$productId = 10;
		$productPkey = 'pid';
		
		$product = new Product;
		$productPkeyOriginal = $product->pkey();
		$product->pkey($productPkey);
		
		$model = $product->model();
		$modelMeta = $product->modelMeta();
		
		$this->assertFalse(array_key_exists($productPkeyOriginal, $model));
		$this->assertFalse(array_key_exists($productPkeyOriginal, $modelMeta));
	}
	
	public function testPkey_PkeyMustBeValidVariableName() {
		
	}
	
	public function testSimilarTo_FieldsMustBeEqual() {
		$product1 = new Product;
		$product2 = new Product;
		
		$this->assertTrue($product1->similarTo($product2));
		$this->assertTrue($product2->similarTo($product1));
	}
	
	public function testSimilarTo_FieldsUpdated() {
		$product1 = new Product;
		$product2 = new Product;
		
		$product1->pkey('pid');
		
		$this->assertFalse($product1->similarTo($product2));
		$this->assertFalse($product2->similarTo($product1));
	}
	
	public function testSimilarTo_WithData() {
		$product1 = new Product;
		$product2 = new Product;
		
		$product1->setName('Product1');
		$product2->setName('Product2');
		
		$this->assertTrue($product1->similarTo($product2));
		$this->assertTrue($product2->similarTo($product1));
	}
	
	public function testTable_ReturnsTable() {
		$product = new Product;
		$productTableReflection = new \ReflectionProperty(get_class($product), 'table');
		$productTableReflection->setAccessible(true);
		$productTable = $productTableReflection->getValue($product);
		
		$this->assertEquals($productTable, $product->table());
	}
	
	public function testTable_CanSetTable() {
		$productTable = 'products';
		
		$product = new Product;
		$product->table($productTable);
		
		$this->assertEquals($productTable, $product->table());
	}
	
	public function testTable_CanSetTableAndGetNewTable() {
		$productTable = 'products';
		
		$product = new Product;
		
		$this->assertEquals($productTable, $product->table($productTable));
	}
	
	public function testTable_RemovesBackticks() {
		$productTableBackticks = '`products`';
		$productTable = 'products';
		$product = new Product;
		$product->table($productTableBackticks);
		
		$this->assertEquals($productTable, $product->table());
	}
	
	public function testTypeBool_IsTrue() {
		$product = new Product;
		
		$product->setAvailable(true);
		$this->assertTrue($product->getAvailable());
	}
	
	public function testTypeBool_IsFalse() {
		$product = new Product;
		
		$product->setAvailable(false);
		$this->assertFalse($product->getAvailable());
	}
	
	public function testTypeBool_IsFalseByDefault() {
		$product = new Product;
		
		$product->setAvailable('string');
		$this->assertFalse($product->getAvailable());
		
		$product->setAvailable(0);
		$this->assertFalse($product->getAvailable());
	}
	
	/**
	 * @dataProvider providerValidDate
	 */
	public function testTypeDate_ValidFormat($date) {
		$product = new Product;
		$product->setDateAvailable($date);
		
		$this->assertEquals($date, $product->getDateAvailable());
	}
	
	/**
	 * @dataProvider providerInvalidDate
	 */
	public function testTypeDate_InvalidFormat($date) {
		$product = new Product;
		$product->setDateAvailable($date);
		
		$this->assertNotEquals($date, $product->getDateAvailable());
	}
	
	/**
	 * @dataProvider providerValidDatetime
	 */
	public function testTypeDatetime_ValidFormat($datetime) {
		$product = new Product;
		$product->setDateCreated($datetime);
		
		$this->assertEquals($datetime, $product->getDateCreated());
	}
	
	/**
	 * @dataProvider providerInvalidDatetime
	 */
	public function testTypeDatetime_InvalidFormat($datetime) {
		$product = new Product;
		$product->setDateCreated($datetime);
		
		$this->assertNotEquals($datetime, $product->getDateCreated());
	}
	
	public function testTypeInteger_IsInteger() {
		$product = new Product;
		$product->setProductId('string');
		
		$this->assertEquals(0, $product->getProductId());
	}
	
	public function testTypeFloat_SetsPrecision() {
		
	}
	
	public function testTypeString_SupportsMaxLengthAscii() {
		
	}
	
	public function testTypeString_SupportsMaxLengthUtf8() {
		
	}
	
	public function testTypeText_AlwaysConvertedToString() {
		
	}
	
	
	
	
	public function providerNonScalarValue() {
		return array(
			array(array(1, 2, 3)),
			array(range(1, 10)),
			array(new \stdClass),
			array(function ($a) { return $a * 2; })
		);
	}
	
	public function providerScalarValue() {
		return array(
			array('some string'),
			array('現，市民派利市的習慣亦有所改變'),
			array(10.99),
			array(10),
			array(true),
			array(false)
		);
	}
	
	public function providerValidDate() {
		return array(
			array('2006-12-12'),
			array('2006-01-01'),
			array('1952-08-10'),
			array('1952-11-10')
		);
	}
	
	public function providerInvalidDate() {
		return array(
			array('2006-12-12 10:00:00.5'),
			array('2006-12-12 10:15'),
			array('2006-12-12 10'),
			array('2006-12-12 12:34PM'),
			array('3012-12-12'),
			array('2010-15-12'),
			array('2010-08-45'),
			array('1834-08-12'),
			array('1834-00-00')
		);
	}
	
	public function providerValidDatetime() {
		return array(
			array('2006-12-12 10:00:00'),
			array('2006-12-12 10:15:00'),
			array('2006-12-12 10:12:32'),
			array('2006-12-12 23:59:59'),
			array('2006-12-12 00:00:00')
		);
	}
	
	public function providerInvalidDatetime() {
		return array(
			array('2006-12-12   10:00:00.5'),
			array('2006-12-12 10:15'),
			array('2006-12-12 10'),
			array('2006-12-12   12:34PM'),
			array('2006-12-12  24:00:00'),
			array('2006-12-12 18:66:00'),
			array('2006-12-12 00:00:66'),
			array('2006-12-12 34:00:66'),
			array('1834-12-12 00:00:00'),
			array('2088-08-45 00:00:00'),
			array('2088-45-12 00:00:00'),
			array('2006-00-12 10:00:15')
		);
	}
	
	
}