<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

require_once 'lib/Model.php';

class ModelTest extends TestCase {

	public function testTableNameCanBeSet() {
		$table_name = 'products';
	
		$model = $this->buildMockModel();
		$model->table($table_name);
		
		$this->assertEquals($table_name, $model->table()); 
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
	
	
}