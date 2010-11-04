<?php

declare(encoding='UTF-8');
namespace DataModeler;

/**
 * Abstract Model class for building FAT, intelligent models. The model is
 * your primary in memory data store. This class should be extended by another
 * class that is a 1:1 relationship with a table or document.
 *
 * @author vmc <vmc@leftnode.com>
 */
abstract class Model {

	private $modelMeta = array();
	private $modelId = NULL;
	private $multibyte = false;

	protected $pkey = NULL;
	protected $table = NULL;

	const SCHEMA_MAXLENGTH = 'maxlength';
	const SCHEMA_PRECISION = 'precision';
	const SCHEMA_TYPE = 'type';
	const SCHEMA_TYPE_PDO = 'pdotype';
	const SCHEMA_TYPE_TYPELESS = 'TYPELESS';

	const SCHEMA_TYPE_DATE_VALUE = 'NOW';
	const SCHEMA_TYPE_DATETIME_VALUE = 'NOW';

	public function __construct() {
		$this->multibyte = extension_loaded('mbstring');

		if ( $this->multibyte ) {
			mb_internal_encoding('UTF-8');
			mb_regex_encoding('UTF-8');
		}

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

	public function incr($field) {
		$value = $this->get($field);
		if ( is_int($value) ) {
			$this->set($field, ++$value);
		}
		return $this;
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

	public function modelId() {
		return $this->modelId;
	}

	public function modelMeta() {
		return $this->modelMeta;
	}

	public function multibyte($multibyte=-1) {
		if ( -1 == $multibyte ) {
			return $this->multibyte;
		}

		if ( !is_bool($multibyte) ) {
			$multibyte = true;
		}
		$this->multibyte = $multibyte;

		return $this->multibyte;
	}

	public function pkey($pkey=NULL) {
		$pkey = trim($pkey);
		if ( !empty($pkey) ) {
			$oldPkey = $this->pkey;

			$pkey = $this->removeBackticks($pkey);
			$this->pkey = $pkey;

			// Copy the old pkey information to the new information
			// Generally you wouldn't change the pkey data mid-program, but
			// who knows what crazy shit people do.
			if ( array_key_exists($oldPkey, $this->modelMeta) ) {
				$this->modelMeta[$pkey] = $this->modelMeta[$oldPkey];

				unset($this->modelMeta[$oldPkey]);
				ksort($this->modelMeta);

				$this->$pkey = $this->$oldPkey;
			}
		}

		return $this->pkey;
	}

	public function similarTo(\DataModeler\Model $model) {
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
	 * PROTECTED METHODS
	 */

	protected function set($field, $value) {
		if ( isset($this->modelMeta[$field]) ) {
			$type = $this->modelMeta[$field][self::SCHEMA_TYPE];
			$method = "type{$type}";

			if ( method_exists($this, $method) ) {
				$this->$field = $this->$method($field, $value);
			}
		}
		return true;
	}

	protected function get($field) {
		if ( isset($this->modelMeta[$field]) ) {
			return $this->$field;
		}
		return NULL;
	}


	/**
	 * PRIVATE METHODS
	 */

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
		$this->setTypePdo($field, $boolean, \PDO::PARAM_INT);
		$boolean = ( !is_bool($boolean) ? false : $boolean );
		return $boolean;
	}

	private function typeDATE($field, $date) {
		if ( !empty($date) ) {
			$parsedDate = date_parse($date);
			$dateRegex = '/^
				(19|20)\d\d-             # Years in range 1900-2099
				(0[1-9]|1[012])-         # Months in range 01-12
				(0[1-9]|[12][0-9]|3[01]) # Days in range 01-31
				$/x';

			if ( count($parsedDate['errors']) > 0 ) {
				$date = self::SCHEMA_TYPE_DATE_VALUE;
			} elseif ( 0 === preg_match($dateRegex, $date) ) {
				$date = self::SCHEMA_TYPE_DATE_VALUE;
			}

			if ( $date == self::SCHEMA_TYPE_DATE_VALUE ) {
				$date = date('Y-m-d');
			}
		}

		$this->setTypePdo($field, $date, \PDO::PARAM_STR);

		return $date;
	}

	private function typeDATETIME($field, $datetime) {
		if ( !empty($datetime) ) {
			$parsedDatetime = date_parse($datetime);
			$datetimeRegex = '/^
				(19|20)\d\d-               # Years in range 1900-2099
				(0[1-9]|1[012])-           # Months in range 01-12
				(0[1-9]|[12][0-9]|3[01])\  # Days in range 01-31
				(0[0-9]|1[0-9]|2[0-3]):    # Hours in range 00-23
				(0[0-9]|[1-5][0-9]):       # Minutes in range 00-59
				(0[0-9]|[1-5][0-9])        # Seconds in range 00-59
				$/x';

			if ( count($parsedDatetime['errors']) > 0 ) {
				$datetime = self::SCHEMA_TYPE_DATETIME_VALUE;
			} elseif ( 0 === preg_match($datetimeRegex, $datetime) ) {
				$datetime = self::SCHEMA_TYPE_DATE_VALUE;
			}

			if ( $datetime == self::SCHEMA_TYPE_DATETIME_VALUE ) {
				$datetime = date('Y-m-d H:i:s');
			}
		}

		$this->setTypePdo($field, $datetime, \PDO::PARAM_STR);

		return $datetime;
	}

	private function typeINTEGER($field, $integer) {
		$this->setTypePdo($field, $integer, \PDO::PARAM_INT);
		return (int)$integer;
	}

	private function typeFLOAT($field, $number) {
		$precision = -1;
		if ( isset($this->modelMeta[$field][self::SCHEMA_PRECISION]) ) {
			$precision = (int)$this->modelMeta[$field][self::SCHEMA_PRECISION];
		}

		$this->setTypePdo($field, $number, \PDO::PARAM_STR);

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

		$this->setTypePdo($field, $string, \PDO::PARAM_STR);

		if ( $maxlength > 0 ) {
			if ( $this->multibyte ) {
				$string = mb_substr($string, 0, $maxlength);
			} else {
				$string = substr($string, 0, $maxlength);
			}
		}

		return $string;
	}

	private function typeTEXT($field, $text) {
		$this->setTypePdo($field, $text, \PDO::PARAM_STR);
		return (string)$text;
	}

	private function typeTYPELESS($field, $text) {
		$this->setTypePdo($field, $text, \PDO::PARAM_STR);
		return $text;
	}

	private function setTypePdo($field, $value, $type) {
		if ( is_null($value) ) {
			$this->modelMeta[$field][self::SCHEMA_TYPE_PDO] = \PDO::PARAM_NULL;
		} else {
			$this->modelMeta[$field][self::SCHEMA_TYPE_PDO] = $type;
		}
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