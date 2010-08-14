<?php

declare(encoding='UTF-8');
namespace DataModeler;

use \DataModeler\Type,
	\DataModeler\Type\BoolType,
	\DataModeler\Type\DateType,
	\DataModeler\Type\DateTimeType,
	\DataModeler\Type\FloatType,
	\DataModeler\Type\IntegerType,
	\DataModeler\Type\StringType,
	\DataModeler\Type\TextType,
	\DataModeler\Type\TypelessType;

require_once 'DataModeler/Type/Bool.php';
require_once 'DataModeler/Type/Date.php';
require_once 'DataModeler/Type/Datetime.php';
require_once 'DataModeler/Type/Float.php';
require_once 'DataModeler/Type/Integer.php';
require_once 'DataModeler/Type/String.php';
require_once 'DataModeler/Type/Text.php';
require_once 'DataModeler/Type/Typeless.php';

/**
 * Abstract Model class for building FAT, intelligent models. The model is
 * your primary in memory data store. This class should be extended to another
 * class that is a 1:1 relationship with a table or document.
 * 
 * @author vmc <vmc@leftnode.com>
 * @version 0.0.10
 */
abstract class Model {
	private $id = NULL;
	private $model = array();
	private $modelId = NULL;
	
	protected $pkey = NULL;
	protected $table = NULL;

	const SCHEMA_DEFAULT = 'default';
	const SCHEMA_MAXLENGTH = 'maxlength';
	const SCHEMA_PRECISION = 'precision';
	const SCHEMA_TYPE = 'type';
	
	public function __construct() {
		$this->modelId = sha1(get_class($this));
		$this->buildSchema();
	}
	
	public function __destruct() {
		$this->model = array();
		$this->modelId = NULL;
	}
	
	public function __call($method, $argv) {
		$argc = count($argv);
		
		$k = $this->convertCamelCaseToUnderscores($method);
		
		if ( 0 === $argc ) {
			/* If the length is 0, assume this is a get() */
			$v = $this->__get($k);
			return $v;
		} else {
			$v = current($argv);
			$this->__set($k, $v);
			
			return $this;
		}
	}
	
	public function __get($key) {
		if ( $key === $this->pkey ) {
			return $this->id;
		} else {
			if ( isset($this->model[$key]) && is_object($this->model[$key]) ) {
				return $this->model[$key]->value;
			}
		}
		
		return NULL;
	}
	
	public function __set($key, $value) {
		if ( isset($this->model[$key]) && is_object($this->model[$key]) ) {
			$this->model[$key]->value = $value;
			
			if ( $key === $this->pkey ) {
				$this->id = $this->model[$key]->value;
			}
		}
		
		ksort($this->model);

		return true;
	}
	
	public function equalTo(Model $model) {
		$modelEquals = true;
		
		$thisModel = $this->model();
		$thatModel = $model->model();
		
		foreach ( $thisModel as $k => $type ) {
			if ( $thisModel[$k]->value !== $thatModel[$k]->value ) {
				$modelEquals = false;
			}
		}
		
		return (
			$this->isA($model) &&
			$this->id() === $model->id() &&
			$modelEquals
		);
	}
	
	public function exists() {
		$id = $this->id();
		return (false === empty($id));
	}
	
	public function field($field, \DataModeler\Type $type) {
		$this->model[$field] = clone $type;
		ksort($this->model);
		
		return $this;
	}
	
	public function id($id = NULL) {
		if ( !empty($id) ) {
			$this->__set($this->pkey, $id);
		}
		return $this->id;
	}
	
	public function isA(Model $model) {
		return (
			$this->table() === $model->table() &&
			$this->modelId() === $model->modelId()
		);
	}
	
	public function load(array $nvp) {
		foreach ( $nvp as $k => $v ) {
			$this->$k = $v;
		}
		return $this;
	}

	public function model() {
		return $this->model;
	}

	public function modelId() {
		return $this->modelId;
	}
	
	public function nvp() {
		$nvp = array();
		foreach ( $this->model as $k => $type ) {
			$nvp[$k] = $type->value;
		}
		return $nvp;
	}
	
	public function pkey($pkey = NULL) {
		$pkey = trim($pkey);
		if ( !empty($pkey) ) {
			$pkey = $this->removeBackticks($pkey);
			$this->pkey = $pkey;
		}
		return $this->pkey;
	}
	
	public function similarTo(Model $model) {
		return (
			$this->isA($model) &&
			array_keys($this->model()) === array_keys($model->model())
		);
	}

	public function table($table = NULL) {
		$table = trim($table);
		if ( !empty($table) ) {
			$table = $this->removeBackticks($table);
			$this->table = $table;
		}
		return $this->table;
	}

	private function convertCamelCaseToUnderscores($v) {
		$v = substr($v, 3);
		$v = strtolower(substr($v, 0, 1)) . substr($v, 1);
		$v = preg_replace('/[A-Z]/', '_\\0', $v);
		$v = strtolower($v);
		return $v;
	}
	
	private function buildSchema() {
		$reflection = new \ReflectionClass(get_class($this));
		$propertyList = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);
		
		$namespace = __NAMESPACE__;
		
		$metaFieldList = array(
			self::SCHEMA_DEFAULT => true,
			self::SCHEMA_MAXLENGTH => true,
			self::SCHEMA_PRECISION => true
		);
		
		$model = array();
		$schema = array();
		$schemaMeta = array();
		
		foreach ( $propertyList as $property ) {
			$metaList = array();
			$metaType = NULL;
			
			$schemaField = $property->getName();
			
			$docComment = trim($property->getDocComment());
			$docComment = str_replace(array('/**', '*/'), array(NULL, NULL), $docComment);
			$matchCount = preg_match_all('#\[([a-z]+) ([a-z0-9 ]+)\]+#i', $docComment, $metaList);

			if ( $matchCount > 0 ) {
				array_shift($metaList);
				
				$len = count($metaList[0]);
				for ( $i=0; $i<$len; $i++ ) {
					$meta = trim(strtolower($metaList[0][$i]));
					
					if ( self::SCHEMA_TYPE == $meta ) {
						$class = ucwords(strtolower($metaList[1][$i]));
						$class = "\\{$namespace}\\Type\\{$class}Type";
						
						if ( class_exists($class) ) {
							$metaType = new $class;
						}
					}
				}
			}

			if ( is_null($metaType) ) {
				$metaType = new \DataModeler\Type\TypelessType;
			}
			
			$metaType->field = $schemaField;
			
			if ( $matchCount > 0 ) {
				$len = count($metaList[0]);
				
				for ( $i=0; $i<$len; $i++ ) {
					$meta = trim(strtolower($metaList[0][$i]));
					
					if ( isset($metaFieldList[$meta]) ) {
						$metaType->$meta = $metaList[1][$i];
						if ( self::SCHEMA_DEFAULT == $meta ) {
							$metaType->value = $metaList[1][$i];
						}
					}
				}
			}
			
			$model[$schemaField] = $metaType;
		}
		
		$this->model = $model;
		
		return true;
	}
	
	private function removeBackticks($value) {
		return str_replace('`', NULL, $value);
	}
	
}