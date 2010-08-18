<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use \DataModeler\Iterator,
	\DataModeler\Model,
	\DataModeler\Adapter\Sql;

class TestCase extends \PHPUnit_Framework_TestCase {
	
	protected $pdo = NULL;
	
	public static function assertArray($a, $message = '') {
		self::assertThat(is_array($a), self::isTrue(), $message);
	}
	
	public static function assertEmptyArray($a, $message = '') {
		self::assertArray($a);
		self::assertEquals(0, count($a), $message);
	}
	
	public static function assertNotEmptyArray($a, $message = '') {
		self::assertArray($a, $message);
		self::assertGreaterThan(0, count($a), $message);
	}

	public static function assertModelList($modelList, $message = '') {
		self::assertArray($modelList, $message);
		foreach ( $modelList as $model ) {
			self::assertTrue($model instanceof \DataModeler\Model, $message);
		}
	}
	
	public static function assertIterator($obj, $message = '') {
		self::assertTrue(is_object($obj), $message);
		self::assertTrue($obj instanceof \DataModeler\Iterator, $message);
	}

	public static function assertModel($obj, $message = '') {
		self::assertTrue(is_object($obj), $message);
		self::assertTrue($obj instanceof \DataModeler\Model, $message);
	}

	public static function assertPdoStatement($obj, $message = '') {
		self::assertTrue(is_object($obj), $message);
		self::assertTrue($obj instanceof \PDOStatement, $message);
	}

	public static function assertSql($obj, $message = '') {
		self::assertTrue(is_object($obj), $message);
		self::assertTrue($obj instanceof \DataModeler\Sql, $message);
	}
	
	protected function buildMockModel($table = NULL, $pkey = NULL) {
		$model = $this->getMockForAbstractClass('\\DataModeler\\Model');
		$model->table($table);
		$model->pkey($pkey);
		
		return $model;
	}
	
	protected function buildMockOrder() {
		$model = $this->buildMockModel('orders', 'order_id');
		return $model;
	}
	
	protected function buildMockProduct() {
		$model = $this->buildMockModel('products', 'product_id');
		return $model;
	}
	
	protected function buildMockUser() {
		$model = $this->buildMockModel('users', 'id');
		return $model;
	}
	
	protected function buildMockPdo($dsn) {
		$pdo = $this->getMock('\\PDO', array(), array($dsn));
		return $pdo;
	}
	
	protected function buildMockType() {
		$type = $this->getMockForAbstractClass('\\DataModeler\\Type');
		return $type;
	}
	
	
	protected function buildPdo() {
		$this->pdo = new \PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		
		$sqlFile = DIRECTORY_DATA . DB_TYPE . '.sql';
		if ( is_file($sqlFile) ) {
			$this->pdo->exec(file_get_contents($sqlFile));
		}
	}
}