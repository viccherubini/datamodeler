<?php

declare(encoding='UTF-8');
namespace DataModeler;

/**
 * Abstract Model class for building FAT, intelligent models. The model is
 * your primary in memory data store. This class should be extended to another
 * class that is a 1:1 relationship with a table or document.
 * 
 * @author vmc <vmc@leftnode.com>
 * @version 0.0.10
 */
abstract class Model {
	private $datetype = NULL;
	private $hasdate = NULL;
	private $id = NULL;
	private $model = array();
	private $modelId = NULL;
	private $pkey = NULL;
	private $schema = array();
	private $table = NULL;
	
	private $properties = array();
	
	const DATETYPE_TIMESTAMP = 2;
	const DATETYPE_NOW = 4;
	
	const TYPE_REF = 'ref';

	const TYPE_BOOL = 2;
	const TYPE_DATE = 4;
	const TYPE_DATETIME = 8;
	const TYPE_FLOAT = 16;
	const TYPE_INTEGER = 32;
	const TYPE_STRING = 64;
	const TYPE_TEXT = 128;
	const TYPE_TIME = 256;
	
	const MAPITEM_TYPE = 'type';
	const MAPITEM_MAXLENGTH = 'maxlength';
	
	public function __construct() {
		$this->modelId = sha1(get_class($this));
		
		$this->buildTableSchema();
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
			
			/* If the key is the pkey of the object, don't allow that to be set. */
			$pkey = $this->pkey();
			if ( $k === $pkey ) {
				$this->id($v);
			} else {
				/* Else assume its a set with the first element of $argv. */
				$this->__set($k, $v);
			}
			
			return $this;
		}
	}
	
	public function __get($key) {
		$pkey = $this->pkey();
		
		if ( $pkey === $key ) {
			return $this->id();
		} else {
			$model = $this->model();
			if ( true === isset($model[$key]) ) {
				return $model[$key];
			}
		}
		return NULL;
	}
	
	public function __set($key, $value) {
		$pkey = $this->pkey();
		
		if ( $key === $pkey ) {
			$this->id($value);
		} else {
			if ( true === $this->isValidField($key) ) {
				$this->model[$key] = $value;
			}
			ksort($this->model);
		}
		return true;
	}
	
	public function datetype($datetype = 0) {
		$datetype = intval($datetype);
		if ( $datetype > 0 ) {
			if ( $datetype != self::DATETYPE_TIMESTAMP && $datetype != self::DATETYPE_NOW ) {
				$datetype = self::DATETYPE_TIMESTAMP;
			}
			$this->datetype = $datetype;
		}
		return $this->datetype;
	}
	
	public function equalTo(Model $model) {
		return (
			$this->isA($model) &&
			$this->id() === $model->id() &&
			$this->model() === $model->model()
		);
	}
	
	public function exists() {
		$id = $this->id();
		return (false === empty($id));
	}
	
	public function hasdate($hasdate = NULL) {
		if ( true === $hasdate || false === $hasdate ) {
			$this->hasdate = $hasdate;
		}
		return $this->hasdate;
	}
	
	public function id($id = NULL) {
		if ( false === empty($id) ) {
			$this->id = $id;
		}
		return $this->id;
	}
	
	public function isA(Model $model) {
		return (
			$this->table() === $model->table() &&
			$this->modelId() === $model->modelId()
		);
	}

	public function model(array $model = array()) {
		if ( false !== current($model) || ( count($model) > 0 ) ) {
			$pkey = $this->pkey();
			if ( true === isset($model[$pkey]) ) {
				$this->id($model[$pkey]);
				unset($model[$pkey]);
			}
			$this->model = $model;
		}
		return $this->model;
	}
	
	public function modelId() {
		return $this->modelId;
	}
	
	public function pkey($pkey = NULL) {
		$pkey = trim($pkey);
		if ( false === empty($pkey) ) {
			$pkey = $this->removeBackticks($pkey);
			$this->pkey = $pkey;
		}
		return $this->pkey;
	}
	
	public function schema($schema = array()) {
		if ( count($schema) > 0 ) {
			$this->schema = $schema;
		}
		return $this->schema;
	}
	
	public function similarTo(Model $model) {
		return (
			$this->isA($model) &&
			array_keys($this->model()) === array_keys($model->model())
		);
	}

	public function table($table = NULL) {
		$table = trim($table);
		if ( false === empty($table) ) {
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
	
	private function isValidField($field) {
		if ( 1 === preg_match('/^[a-z0-9_\-.]+$/i', $field) ) {
			return true;
		}
		return false;
	}
	
	private function buildTableSchema() {
		$reflection = new \ReflectionClass(get_class($this));
		$properties = $reflection->getProperties();
		
		$schema = array();
		$model = array();
		
		foreach ( $properties as $property ) {
			$propertyName = $property->getName();
			$schema[$propertyName] = array();
			
			$docComment = $property->getDocComment();
			$docComment = str_replace(array('/**', '*/'), array(NULL, NULL), $docComment);
			
			$matchCount = preg_match_all('#\[([a-z]+ [a-z0-9]+)\]+#i', $docComment, $foundMatches);
			if ( $matchCount > 0 ) {
				$foundMatches = array_pop($foundMatches);
				
				foreach ( $foundMatches as $mapItem ) {
					$mapItemBits = explode(' ', $mapItem);
					
					if ( 2 != count($mapItemBits) ) {
						continue;
					}
					
					$mapItemKey = strtolower(trim($mapItemBits[0]));
					$mapItemValue = trim($mapItemBits[1]);
					
					switch ( $mapItemKey ) {
						case self::MAPITEM_TYPE: {
							$mapItemValue = strtoupper($mapItemValue);
							$mapItemDefine = "self::TYPE_{$mapItemValue}";
							
							if ( defined($mapItemDefine) ) {
								$schema[$propertyName][$mapItemKey] = constant($mapItemDefine);
							}
							break;
						}
						
						case self::MAPITEM_MAXLENGTH: {
							$maxlength = intval($mapItemValue);
							if ( $maxlength > 0 ) {
								$schema[$propertyName][$mapItemKey] = $maxlength;
							}
							break;
						}
					}
				}
				
			}
		}
		
		$this->schema($schema);
		return true;
	}
	
	private function removeBackticks($value) {
		return str_replace('`', NULL, $value);
	}
	
	private function buildReferenceTable() {
		
		
		
	}
	
	private function parseDocComment($comment) {
		// Empty method, move the parsing from the buildSchemaTable() method
		// to here. Return an list of key value pairs.
		
	}
}
