<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

require_once 'lib/Model.php';

class ModelTest extends TestCase {

	public function testMagicCaller_CanSetAndGetValue() {
		$model = $this->buildMockModel();
		$model->setFirstname('Vic');
		
		$this->assertEquals('Vic', $model->getFirstname());
	}


	public function testMagicCaller_SetsModelArrayProperly() {
		$firstname = 'Vic';
		$lastname = 'Cherubini';
		
		$model = $this->buildMockModel();
		$model->setFirstname($firstname);
		$model->setLastname($lastname);
		
		$this->assertEquals(array('firstname' => $firstname, 'lastname' => $lastname), $model->model());
	}
	
	
	public function testMagicCaller_DoesNotAllowPkeyToBeSet() {
		$pkey = 'product_id';
		
		$model = $this->buildMockModel();
		$model->pkey($pkey);
		
		$model->setProductId(10);
		
		$this->assertEmptyArray($model->model());
	}
	

	public function testMagicGetter_ReturnsValueFromModel() {
		$firstname = "Vic";
		
		$model = $this->buildMockModel();
		$model->firstname = $firstname;
		
		$this->assertEquals($firstname, $model->firstname);
	}


	public function testMagicGetter_ReturnsNullWhenKeyDoesNotExist() {
		$model = $this->buildMockModel();
		
		$this->assertNull($model->firstname);
	}
	
	
	public function testMagicGetter_CanGetPkey() {
		$product_id = 10;
		
		$model = $this->buildMockModel();
		$model->pkey('product_id');
		$model->id($product_id);
		
		$this->assertEquals($product_id, $model->product_id);
	}


	public function testMagicSetter_SetsValueInModel() {
		$expected_model_array = array('first_name' => "Vic Cherubini");
		
		$model = $this->buildMockModel();
		$model->first_name = "Vic Cherubini";
		
		$this->assertEquals($expected_model_array, $model->model());
	}
	
	
	public function testMagicSetter_CannotSetPkeyInModel() {
		$model = $this->buildMockModel();
		$model->pkey('product_id');
		
		$model->product_id = 10;
		$this->assertEmptyArray($model->model());
	}
	
	
	public function testMagicSetter_CanSetPkeyInObject() {
		$pkey = "product_id";
		$product_id = 10;
		
		$model = $this->buildMockModel();
		$model->pkey($pkey);
		$model->$pkey = 10;
		
		$this->assertEquals($product_id, $model->id());
	}
	
	/**
	 * @dataProvider providerInvalidFieldName
	 */
	public function testMagicSetter_DoesNotAllowMalignedFieldToBeSet($field) {
		$value = "invalid value";
		
		$model = $this->buildMockModel();
		$model->$field = $value;
	
		$this->assertEmptyArray($model->model());
	}
	

	public function testDatetype_IsTimestampByDefault() {
		$model = $this->buildMockModel();
		$model->datetype(100);
		
		$this->assertEquals(\DataModeler\Model::DATETYPE_TIMESTAMP, $model->datetype());
	}
	
	
	public function testDatetype_CanBeSqlNow() {
		$model = $this->buildMockModel();
		$model->datetype(\DataModeler\Model::DATETYPE_NOW);
		
		$this->assertEquals(\DataModeler\Model::DATETYPE_NOW, $model->datetype());
	}
	
	
	public function testDatetype_CanBeTimestamp() {
		$model = $this->buildMockModel();
		$model->datetype(\DataModeler\Model::DATETYPE_TIMESTAMP);
		
		$this->assertEquals(\DataModeler\Model::DATETYPE_TIMESTAMP, $model->datetype());
	}
	
	
	public function testHasdate_MustBeBoolean() {
		$model = $this->buildMockModel();
		$model->hasdate(103);
		
		$this->assertNull($model->hasdate());
	}
	
	
	public function testHasdate_CanBeTrue() {
		$model = $this->buildMockModel();
		$model->hasdate(true);
		
		$this->assertTrue($model->hasdate());
	}
	
	
	public function testHasdate_CanBeFalse() {
		$model = $this->buildMockModel();
		$model->hasdate(false);
		
		$this->assertFalse($model->hasdate());
	}
	
	
	public function testId_IsInitiallyEmpty() {
		$model = $this->buildMockModel();
		$this->assertNull($model->id());
	}
	
	
	public function testId_CanBeSet() {
		$id = 10;
		
		$model = $this->buildMockModel();
		$model->id($id);
		
		$this->assertEquals($id, $model->id());
	}
	
	
	public function testModel_IsInitiallyEmpty() {
		$model = $this->buildMockModel();
		
		$this->assertEmptyArray($model->model());
	}
	
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testModel_MustBeArray() {
		$model = $this->buildMockModel();
		$model->model(10);
	}
	
	
	public function testModel_FirstElementCanBeFalse() {
		$model = $this->buildMockModel();
		$model->model(array(false));
		
		$this->assertNotEmptyArray($model->model());
	}
	
	
	public function testModel_DoesNotAllowPkeyToBeSet() {
		$array = array('product_id' => 10);
		$pkey = 'product_id';
		
		$model = $this->buildMockModel();
		$model->pkey($pkey);
		$model->model($array);
		
		$this->assertEmptyArray($model->model());
	}
	
	
	public function testExists_IsTrueWhenIdIsSet() {
		$pkey = 'product_id';
		$id = 10;
		
		$model = $this->buildMockModel();
		$model->pkey($pkey);
		$model->id($id);
		
		$this->assertTrue($model->exists());
	}
	
	
	public function testExists_IsFalseWhenIdIsNotSet() {
		$model = $this->buildMockModel();
		
		$this->assertFalse($model->exists());
	}
	
	
	public function testPkey_CannotContainBackticks() {
		$pkey_with_backticks = '`p.product_id`';
		$pkey_without_backticks = 'p.product_id';
		
		$model = $this->buildMockModel();
		$model->pkey($pkey_with_backticks);
		
		$this->assertEquals($pkey_without_backticks, $model->pkey());
	}
	
	
	public function testTable_CanBeSet() {
		$table = 'products';
	
		$model = $this->buildMockModel();
		$model->table($table);
		
		$this->assertEquals($table, $model->table()); 
	}
	
	
	/**
	 * @dataProvider providerValidTableName
	 */
	public function testTable_CanOnlyContainValidCharacters($table) {
		$model = $this->buildMockModel();
		$model->table($table);
		
		$this->assertEquals($table, $model->table());
	}
	
	
	public function testTable_CannotContainBackticks() {
		$table_with_backticks = '`table_name`';
		$table_without_backticks = 'table_name';
		
		$model = $this->buildMockModel();
		$model->table($table_with_backticks);
		
		$this->assertEquals($table_without_backticks, $model->table());
	}
	
	
	public function providerValidTableName() {
		return array(
			array('products'),
			array('p.products'),
			array('product_list'),
			array('product-list'),
			array('p.product_list'),
			array('p.product-list')
		);
	}
	
	
	public function providerInvalidFieldName() {
		return array(
			array('vic[]cherubini'),
			array('vic cherubini'),
			array('vic*cherubini'),
			array('vic@cherubini'),
			array('vic()cherubini'),
			array('vic&cherubini')
		);
	}
}