<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use \DataModelerTest\TestCase,
	\DataModeler\Model,
	\DataModeler\Sql,
	\DataModeler\SqlResult;

require_once 'DataModeler/Sql.php';
require_once 'DataModeler/SqlResult.php';

require_once 'Product.php';
require_once 'User.php';

class SqlTest extends TestCase {
	
	public function setUp() {
		$this->buildPdo();
	}

	public function tearDown() {
		$this->pdo = NULL;
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testAttachPdo_MustBePdo() {
		$sql = new Sql;
		
		$sql->attachPdo(NULL);
	}
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testPrepare_RequiresPdo() {
		$sql = new Sql;
		
		$sql->prepare($this->buildMockModel());
	}
	
	/**
	 * @dataProvider providerProductWhereClause
	 */
	public function testPrepare_ReturnsSqlResult($where) {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$sqlResult = $sql->prepare($this->buildMockProduct(), $where);
		
		$this->assertTrue($sqlResult instanceof \DataModeler\SqlResult);
	}
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testPreparePkey_RequiresPdo() {
		$sql = new Sql;
		
		$sql->preparePkey($this->buildMockModel());
	}
	
	public function testPreparePkey_ReturnsSqlResult() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$sqlResult = $sql->preparePkey($this->buildMockProduct());
		
		$this->assertTrue($sqlResult instanceof \DataModeler\SqlResult);
	}
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testSave_RequiresPdo() {
		$sql = new Sql;
		
		$sql->save($this->buildMockProduct());
	}
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testQuery_RequiresPdo() {
		$sql = new Sql;
		
		$sql->query('SELECT * FROM products');
	}
	
	/**
	 * @expectedException \DataModeler\Exception
	 * @dataProvider providerInvalidQuery
	 */
	public function testQuery_RequiresValidQuery($query) {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$sql->query($query);
	}
	
	/**
	 * @dataProvider providerValidQuery
	 */
	public function testQuery_ReturnsSqlResult($query) {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$sqlResult = $sql->query($query);
		$this->assertSqlResult($sqlResult);
	}
	
	
	
	
	
	
	/**
	 * @expectedException \DataModeler\Exception
	 */
	public function testCountOf_RequiresPdo() {
		$sql = new Sql;
		
		$sql->countOf($this->buildMockProduct());
	}
	
	/**
	 * @dataProvider providerProductWhereClauseAndParameters
	 */
	public function testCountOf_UsesWhere($where, $parameters) {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$countOf = $sql->countOf($this->buildMockProduct(), $where, $parameters);
		
		$this->assertGreaterThan(0, $countOf);
	}
	
	public function testNow_ReturnsDatetime() {
		$sql = new Sql;
		$datetimeRegex = '/^
			(19|20)\d\d-               # Years in range 1900-2099
			(0[1-9]|1[012])-           # Months in range 01-12
			(0[1-9]|[12][0-9]|3[01])\  # Days in range 01-31
			(0[0-9]|1[0-9]|2[0-3]):    # Hours in range 00-23
			(0[0-9]|[1-5][0-9]):       # Minutes in range 00-59
			(0[0-9]|[1-5][0-9])        # Seconds in range 00-59
			$/x';
			
		$now = $sql->now();
		$parsedDate = date_parse($now);
		$this->assertEquals(0, count($parsedDate['errors']));
		$this->assertEquals(1, preg_match($datetimeRegex, $now));
		
		$now = $sql->now(-1000);
		$parsedDate = date_parse($now);
		$this->assertEquals(0, count($parsedDate['errors']));
		$this->assertEquals(1, preg_match($datetimeRegex, $now));
		
		$now = $sql->now(103990239);
		$parsedDate = date_parse($now);
		$this->assertEquals(0, count($parsedDate['errors']));
		$this->assertEquals(1, preg_match($datetimeRegex, $now));
	}
	
	public function testNow_ReturnsDate() {
		$sql = new Sql;
		$dateRegex = '/^
			(19|20)\d\d-             # Years in range 1900-2099
			(0[1-9]|1[012])-         # Months in range 01-12
			(0[1-9]|[12][0-9]|3[01]) # Days in range 01-31
			$/x';
		
		$now = $sql->now(0, true);
		$parsedDate = date_parse($now);
		$this->assertEquals(0, count($parsedDate['errors']));
		$this->assertEquals(1, preg_match($dateRegex, $now));
		
		$now = $sql->now(-1000, true);
		$parsedDate = date_parse($now);
		$this->assertEquals(0, count($parsedDate['errors']));
		$this->assertEquals(1, preg_match($dateRegex, $now));
		
		$now = $sql->now(103990239, true);
		$parsedDate = date_parse($now);
		$this->assertEquals(0, count($parsedDate['errors']));
		$this->assertEquals(1, preg_match($dateRegex, $now));
	}
	
	public function testGetPdo_ReturnsPdoObject() {
		$sql = new Sql;
		$sql->attachPdo($this->pdo);
		
		$this->assertTrue($sql->getPdo() instanceof \PDO);
	}
	
	public function providerProductWhereClause() {
		return array(
			array('product_id = ?'),
			array('product_id > ?'),
			array('product_id != ?'),
			array('product_id != ? AND name != ?'),
			array('price <> ?'),
			array('sku = ?'),
			array(NULL)
		);
	}

	public function providerProductWhereClauseAndParameters() {
		return array(
			array('product_id = ?', array(1)),
			array('product_id > ?', array(1)),
			array('product_id != ?', array(2)),
			array('product_id != ? AND name != ?', array(2, 'Product 1'))
		);
	}
	
	public function providerValidQuery() {
		return array(
			array('SELECT * FROM products WHERE product_id > ?'),
			array('SELECT * FROM orders INNER JOIN users ON customer_id = id WHERE order_id > ?'),
			array('SELECT * FROM orders LEFT JOIN users ON customer_id = id WHERE order_id > ?'),
			array('SELECT * FROM orders INNER JOIN users ON customer_id = id WHERE username <> ? AND favorite_book = ?'),
			array('SELECT SUM(o.total) FROM orders o WHERE o.total > 0')
		);
	}
	
	public function providerInvalidQuery() {
		return array(
			array('SELECT * FROM products_missing WHERE product_id > ?'),
			array('SELECT * FROM orders INNER JOIN users USING(customer_id) WHERE order_id > ?'),
			array('SELECT * FROM orders LEFT JOIN users USING(customer_id) WHERE order_id > ?'),
			array('SELECT * FROM orders INNER JOIN users USING(id) WHERE username <> ? AND favorite_book = ?'),
			array('SELECT SUM(o.total) FROM orders o WHERE o.total_price > 0'),
			array('SELECT missing_field FROM products WHERE products_price > ? AND product_name != ?')
		);
	}
	
}