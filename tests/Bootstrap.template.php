<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

require_once 'PHPUnit/Framework.php';
require_once 'TestCase.php';

$dataModelerTestPath = dirname(__FILE__);
$dataModelerLibPath  = $dataModelerTestPath . '/../';

define('DS', DIRECTORY_SEPARATOR, false);
define('DIRECTORY_TESTS', $dataModelerTestPath . DS, false);
define('DIRECTORY_DATA', DIRECTORY_TESTS . 'data' . DS, false);
define('DIRECTORY_MODELS', DIRECTORY_TESTS . 'models' . DS, false);

$includeList = array(DIRECTORY_MODELS, $dataModelerLibPath, $dataModelerTestPath, get_include_path());
set_include_path(implode(PATH_SEPARATOR, $includeList));

require_once 'DataModeler/Exception.php';

$dbType = 'sqlite';
$dbName = 'datamodeler_tests';
$dbHost = 'localhost';

switch ( $dbType ) {
	case 'mysql': {
		$dbDsn = "dbname={$dbName};host={$dbHost}";
		break;
	}
	
	case 'sqlite': {
		$dbDsn = ":memory:";
		break;
	}
}

define('DB_TYPE', $dbType, false);
define('DB_DSN', "{$dbType}:{$dbDsn}", false);
define('DB_USERNAME', '', false);
define('DB_PASSWORD', '', false);