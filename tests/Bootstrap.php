<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

require_once 'PHPUnit/Framework.php';
require_once 'TestCase.php';

$data_modeler_test_path = dirname(__FILE__);
$data_modeler_lib_path  = $data_modeler_test_path . '/../';
set_include_path(get_include_path() . PATH_SEPARATOR . $data_modeler_lib_path . PATH_SEPARATOR . $data_modeler_test_path);

define('DS', DIRECTORY_SEPARATOR, false);
define('DIRECTORY_TESTS', $data_modeler_test_path . DS, false);
define('DIRECTORY_DATA', DIRECTORY_TESTS . 'data' . DS, false);