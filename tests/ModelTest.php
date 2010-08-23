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
	
	public function test__Call_CanOnlySetScalarValue() {
		
	}
	
	public function test__Call_CanOnlySetPredefinedField() {
		
	}
	
	public function test__Call_SetsAllowChaining() {
		
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
		
	}
	
	public function testExists_IdIsEmpty() {
		
	}

	public function testId_ReturnsId() {
		
	}
	
	public function testId_CanSetId() {
		
	}
	
	public function testId_CanSetIdAndGetNewId() {
		
	}
	
	public function testIsA_ModelNamesMustBeEqual() {
		
	}
	
	public function testIsA_ModelNamesAreNotEqual() {
		
	}
	
	public function testLoad_MustLoadArray() {
		
	}
	
	public function testLoad_CanOnlyLoadPredefinedFields() {
		
	}
	
	public function testModel_ReturnsNvpArray() {
		
	}
	
	public function testModel_FieldsAlwaysSorted() {
		
	}
	
	public function testModelMeta_FieldsAlwaysSorted() {
		
	}
	
	public function testModelId_IsHashOfClass() {
		
	}
	
	public function testModelId_IsEqualForSameClasses() {
		
	}
	
	public function testPkey_ReturnsPkey() {
		
	}
	
	public function testPkey_CanSetPkey() {
		
	}
	
	public function testPkey_CanSetPkeyAndGetNewPkey() {
		
	}
	
	public function testSimilarTo_FieldsMustBeEqual() {
		
	}
	
	public function testSimilarTo_WithData() {
		
	}
	
	public function testSimilarTo_WithoutData() {
		
	}
	
	public function testSimilarTo_DataUpdated() {
		
	}
	
	public function testTable_ReturnsTable() {
		
	}
	
	public function testTable_CanSetTable() {
		
	}
	
	public function testTable_CanSetTableAndGetNewTable() {
		
	}
	
	
}