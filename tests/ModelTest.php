<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

require_once 'lib/Model.php';

class ModelTest extends TestCase {

	


	public function testDatetypeIsTimestampByDefault() {
		$model = $this->buildMockModel();
		$model->datetype(100);
		
		$this->assertEquals(\DataModeler\Model::DATETYPE_TIMESTAMP, $model->datetype());
	}
	
	
	public function testDatetypeCanBeNow() {
		$model = $this->buildMockModel();
		$model->datetype(\DataModeler\Model::DATETYPE_NOW);
		
		$this->assertEquals(\DataModeler\Model::DATETYPE_NOW, $model->datetype());
	}
	
	
	public function testDatetypeCanBeTimestamp() {
		$model = $this->buildMockModel();
		$model->datetype(\DataModeler\Model::DATETYPE_TIMESTAMP);
		
		$this->assertEquals(\DataModeler\Model::DATETYPE_TIMESTAMP, $model->datetype());
	}
	
	
	public function testIdIsInitiallyEmpty() {
		$model = $this->buildMockModel();
		$this->assertNull($model->id());
	}
	
	
	public function testIdCanBeSet() {
		$id = 10;
		
		$model = $this->buildMockModel();
		$model->id($id);
		
		$this->assertEquals($id, $model->id());
	}
	
	
	public function testModelIsInitiallyEmpty() {
		$model = $this->buildMockModel();
		
		$this->assertEmptyArray($model->model());
	}
	
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testModelMustBeArray() {
		$model = $this->buildMockModel();
		$model->model(10);
	}
	
	
	public function testFirstElementOfModelCanBeFalse() {
		$model = $this->buildMockModel();
		$model->model(array(false));
		
		$this->assertNotEmptyArray($model->model());
	}
	
	
	public function testTableNameCanBeSet() {
		$table = 'products';
	
		$model = $this->buildMockModel();
		$model->table($table);
		
		$this->assertEquals($table, $model->table()); 
	}
	
	
	/**
	 * @dataProvider providerValidTableNameList
	 */
	public function testTableNameCanOnlyContainValidCharacters($table) {
		$model = $this->buildMockModel();
		$model->table($table);
		
		$this->assertEquals($table, $model->table());
	}
	
	
	public function testTableNameHasBackticksRemoved() {
		$table_with_backticks = '`table_name`';
		$table_without_backticks = 'table_name';
		
		$model = $this->buildMockModel();
		$model->table($table_with_backticks);
		
		$this->assertEquals($table_without_backticks, $model->table());
	}
	
	
	
	public function providerValidTableNameList() {
		return array(
			array('products'),
			array('p.products'),
			array('product_list'),
			array('product-list'),
			array('p.product_list'),
			array('p.product-list')
		);
		
	}
}