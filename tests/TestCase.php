<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use \DataModeler\Iterator,
	\DataModeler\Model,
	\DataModeler\Adapter\Sql;

class TestCase extends \PHPUnit_Framework_TestCase {
	
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
			self::assertTrue($model instanceof Model, $message);
		}
	}
	
	public static function assertIterator($obj, $message = '') {
		self::assertTrue(is_object($obj), $message);
		self::assertTrue($obj instanceof Iterator, $message);
	}

	public static function assertModel($obj, $message = '') {
		self::assertTrue(is_object($obj), $message);
		self::assertTrue($obj instanceof Model, $message);
	}

	public static function assertPdoStatement($obj, $message = '') {
		self::assertTrue(is_object($obj), $message);
		self::assertTrue($obj instanceof \PDOStatement, $message);
	}

	public static function assertSql($obj, $message = '') {
		self::assertTrue(is_object($obj), $message);
		self::assertTrue($obj instanceof Sql, $message);
	}
	
	protected function buildMockAdapter() {
		$adapter = $this->getMockForAbstractClass('\DataModeler\Adapter');
		return $adapter;
	}
	
	protected function buildMockModel($table = NULL, $pkey = NULL) {
		$model = $this->getMockForAbstractClass('\DataModeler\Model');
		$model->table($table);
		$model->pkey($pkey);
		
		return $model;
	}
	
	protected function buildMockProduct() {
		$model = $this->buildMockModel('products', 'id');
		return $model;
	}
	
	protected function buildMockUser() {
		$model = $this->buildMockModel('users', 'id');
		return $model;
	}
	
	protected function buildMockPdo($dsn) {
		$pdo = $this->getMock('\PDO', array(), array($dsn));
		return $pdo;
	}
}