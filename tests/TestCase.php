<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

class TestCase extends \PHPUnit_Framework_TestCase {

	protected $pdo = NULL;
	protected $loadedSql = NULL;

	// This is included here so subclasses can be empty and not have a fake test
	public function testTrue() {
		$this->assertTrue(true);
	}

	public static function assertArray($a, $message=NULL) {
		self::assertTrue(is_array($a), $message);
	}

	public static function assertSqlResult($sqlResult, $message=NULL) {
		self::assertTrue($sqlResult instanceof \DataModeler\SqlResult);
	}

	protected function buildMockModel($table=NULL, $pkey=NULL) {
		$model = $this->getMockForAbstractClass('\\DataModeler\\Model');
		$model->table($table);
		$model->pkey($pkey);

		return $model;
	}

	protected function buildMockProduct() {
		$model = $this->buildMockModel('products', 'product_id');
		return $model;
	}

	protected function buildPdo() {
		$this->pdo = new \PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

		if ( empty($this->loadedSql) ) {
			$sqlFile = DIRECTORY_DATA . DB_TYPE . '.sql';
			if ( is_file($sqlFile) ) {
				$this->loadedSql = file_get_contents($sqlFile);
				$this->pdo->exec($this->loadedSql);
			}
		}
	}

}