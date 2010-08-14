<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

require_once 'PHPUnit/Framework.php';
require_once 'TestCase.php';

$data_modeler_test_path = dirname(__FILE__);
$data_modeler_lib_path  = $data_modeler_test_path . '/../';
set_include_path($data_modeler_lib_path . PATH_SEPARATOR . $data_modeler_test_path . PATH_SEPARATOR . get_include_path());

require_once 'DataModeler/Type.php';
require_once 'DataModeler/Exception.php';

define('DS', DIRECTORY_SEPARATOR, false);
define('DIRECTORY_TESTS', $data_modeler_test_path . DS, false);
define('DIRECTORY_DATA', DIRECTORY_TESTS . 'data' . DS, false);
define('DIRECTORY_MODELS', DIRECTORY_TESTS . 'models' . DS, false);

$dbType = 'sqlite';
$dbName = 'datamodeler_tests';
$dbHost = '127.0.0.1';

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