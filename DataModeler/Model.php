<?php

declare(encoding='UTF-8');
namespace DataModeler;

/**
 * Abstract Model class for building FAT, intelligent models. The model is
 * your primary in memory data store. This class should be extended by another
 * class that is a 1:1 relationship with a table or document.
 * 
 * @author vmc <vmc@leftnode.com>
 * @version 0.0.10
 */
abstract class Model {
	private $_id = NULL;
	
	private $model = array();
	private $modelMeta = array();
	private $modelId = NULL;
	
	protected $pkey = NULL;
	protected $table = NULL;

	const SCHEMA_MAXLENGTH = 'maxlength';
	const SCHEMA_PRECISION = 'precision';
	const SCHEMA_TYPE = 'type';
	const SCHEMA_TYPE_TYPELESS = 'TYPELESS';
	
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
			return $this->_id;
		} else {
			if ( isset($this->model[$key]) ) {
				return $this->model[$key];
			}
		}
		
		return NULL;
	}
	
	public function __set($field, $value) {
		if ( isset($this->modelMeta[$field]) ) {
			$type = $this->modelMeta[$field][self::SCHEMA_TYPE];
			$method = "type{$type}";
			
			if ( method_exists($this, $method) ) {
				$this->model[$field] = $this->$method($field, $value);
			}
			
			if ( $field == $this->pkey ) {
				$this->_id = $this->model[$field];
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
	
	public function id($id = 0) {
		if ( $id > 0 ) {
			$this->_id = $id;
		}
		return $this->_id;
	}
	
	public function isA(Model $model) {
		return (
			$this->table() === $model->table() &&
			$this->modelId() === $model->modelId()
		);
	}
	
	public function load(array $nvp) {
		foreach ( $nvp as $k => $v ) {
			$this->__set($k, $v);
		}
		return $this;
	}

	public function model() {
		return $this->model;
	}

	public function modelId() {
		return $this->modelId;
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

		$model = array();
		
		$schema = array();
		$schemaMeta = array();
		
		foreach ( $propertyList as $property ) {
			$metaList = array();
			$metaType = NULL;
			
			$maxlength = -1;
			$precision = -1;
			
			$property->setAccessible(true);
			$defaultValue = $property->getValue($this);
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
						$metaType = strtoupper($metaList[1][$i]);
					} else {
						$this->modelMeta[$schemaField][$meta] = $metaList[1][$i];
					}
				}
			}

			if ( empty($metaType) ) {
				$metaType = self::SCHEMA_TYPE_TYPELESS;
			}
			
			$this->modelMeta[$schemaField][self::SCHEMA_TYPE] = $metaType;
			$this->__set($schemaField, $defaultValue);
		}
		
		return true;
	}
	
	private function removeBackticks($value) {
		return str_replace('`', NULL, $value);
	}
	
	
	
	private function typeBOOL($field, $boolean) {
		$boolean = ( !is_bool($boolean) ? false : $boolean );
		return $boolean;
	}
	
	private function typeDATE($field, $date) {
		$parsedDate = date_parse($date);
		if ( count($parsedDate['errors']) > 0 ) {
			$date = NULL;
		}
		return $date;
	}
	
	private function typeDATETIME($field, $datetime) {
		$parsedDate = date_parse($datetime);
		if ( count($parsedDate['errors']) > 0 ) {
			$datetime = NULL;
		}
		return $datetime;
	}
	
	private function typeINTEGER($field, $integer) {
		return (int)$integer;
	}

	private function typeFLOAT($field, $number) {
		$precision = -1;
		if ( isset($this->modelMeta[$field][self::SCHEMA_PRECISION]) ) {
			$precision = (int)$this->modelMeta[$field][self::SCHEMA_PRECISION];
		}
		
		$number = (float)$number;
		if ( $precision > -1 ) {
			$number = round($number, $precision);
		}
		
		return $number;
	}

	private function typeSTRING($field, $string) {
		$maxlength = -1;
		if ( isset($this->modelMeta[$field][self::SCHEMA_MAXLENGTH]) ) {
			$maxlength = (int)$this->modelMeta[$field][self::SCHEMA_MAXLENGTH];
		}
		
		if ( $maxlength > 0 ) {
			$string = substr($string, 0, $maxlength);
		}
		return $string;
	}
	
	private function typeTEXT($field, $text) {
		return (string)$text;
	}

	private function typeTYPELESS($field, $text) {
		return $text;
	}
	
}