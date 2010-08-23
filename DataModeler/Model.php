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

	private $modelMeta = array();
	private $modelId = NULL;
	
	protected $pkey = NULL;
	protected $table = NULL;

	const SCHEMA_MAXLENGTH = 'maxlength';
	const SCHEMA_PRECISION = 'precision';
	const SCHEMA_TYPE = 'type';
	const SCHEMA_TYPE_TYPELESS = 'TYPELESS';
	
	const SCHEMA_TYPE_DATE_VALUE = 'NOW';
	const SCHEMA_TYPE_DATETIME_VALUE = 'NOW';
	
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
			$v = $this->get($k);
			return $v;
		} else {
			$v = current($argv);
			if ( is_scalar($v) ) {
				$this->set($k, $v);
			}
			
			return $this;
		}
	}

	public function equalTo(\DataModeler\Model $model) {
		$modelEquals = true;
		
		$modelMeta = $this->modelMeta;
		
		foreach ( $modelMeta as $k => $meta ) {
			if ( !property_exists($model, $k) || $this->$k !== $model->$k ) {
				$modelEquals = false;
			}
		}
		
		return ( $this->isA($model) && $this->id() === $model->id() && $modelEquals );
	}
	
	public function exists() {
		$id = $this->id();
		return (!empty($id));
	}
	
	public function id($id = 0) {
		$pkey = $this->pkey;
		if ( $id > 0 ) {
			$this->set($pkey, $id);
		}
		return $this->$pkey;
	}
	
	public function isA(\DataModeler\Model $model) {
		return ( $this->table() === $model->table() && $this->modelId() === $model->modelId() );
	}
	
	public function load(array $modelData) {
		foreach ( $modelData as $field => $value ) {
			$this->set($field, $value);
		}
		return $this;
	}

	public function model() {
		$model = array();
		$fields = array_keys($this->modelMeta);
		
		foreach ( $fields as $field ) {
			$model[$field] = $this->$field;
		}
		ksort($model);
		
		return $model;
	}
	
	public function modelMeta() {
		return $this->modelMeta;
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
		$keys1 = array_keys($this->modelMeta);
		$keys2 = array_keys($model->modelMeta());
		
		return ( $this->isA($model) && $keys1 === $keys2 );
	}

	public function table($table = NULL) {
		$table = trim($table);
		if ( !empty($table) ) {
			$table = $this->removeBackticks($table);
			$this->table = $table;
		}
		return $this->table;
	}
	
	
	/**
	 * ##################################################
	 * PRIVATE METHODS
	 * ##################################################
	 */
	
	private function set($field, $value) {
		if ( isset($this->modelMeta[$field]) ) {
			$type = $this->modelMeta[$field][self::SCHEMA_TYPE];
			$method = "type{$type}";
			
			if ( method_exists($this, $method) ) {
				$this->$field = $this->$method($field, $value);
			}
		}
		return true;
	}
	
	private function get($field) {
		if ( isset($this->modelMeta[$field]) ) {
			return $this->$field;
		}
		return NULL;
	}
	
	private function buildSchema() {
		$reflection = new \ReflectionClass(get_class($this));
		$propertyList = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
		
		foreach ( $propertyList as $property ) {
			$metaList = array();
			$metaType = NULL;

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
			$this->set($schemaField, $defaultValue);
		}
		
		ksort($this->modelMeta);
		
		return true;
	}
	
	private function typeBOOL($field, $boolean) {
		$boolean = ( !is_bool($boolean) ? false : $boolean );
		return $boolean;
	}
	
	private function typeDATE($field, $date) {
		if ( !empty($date) ) {
			$parsedDate = date_parse($date);
			if ( count($parsedDate['errors']) > 0 ) {
				$date = self::SCHEMA_TYPE_DATE_VALUE;
			}
			
			if ( $date == self::SCHEMA_TYPE_DATE_VALUE ) {
				$date = date('Y-m-d');
			}
		}
		
		return $date;
	}
	
	private function typeDATETIME($field, $datetime) {
		if ( !empty($datetime) ) {
			$parsedDate = date_parse($datetime);
			if ( count($parsedDate['errors']) > 0 ) {
				$datetime = self::SCHEMA_TYPE_DATETIME_VALUE;
			}
			
			if ( $datetime == self::SCHEMA_TYPE_DATETIME_VALUE ) {
				$datetime = date('Y-m-d H:i:s');
			}
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
	
	private function convertCamelCaseToUnderscores($v) {
		$v = substr($v, 3);
		$v = strtolower(substr($v, 0, 1)) . substr($v, 1);
		$v = preg_replace('/[A-Z]/', '_\\0', $v);
		$v = strtolower($v);
		return $v;
	}
	
	private function removeBackticks($value) {
		return str_replace('`', NULL, $value);
	}
	
}