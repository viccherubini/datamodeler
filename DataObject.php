<?php

/**
 * Abstract class that handles data efficiently. Allows any number of 
 * unnamed getMethod() and setMethod()'s. This does not tie into any database
 * so any business logic can work without a database or external POST/SESSION
 * data. The methods in this class are named without get/set and operate like that
 * because there could be a getTable(), or setChildren() class for example.
 * Thus, a more succinct $data_object->table('new_table') works better.
 * @author vmc <vmc@leftnode.com>
 */
abstract class DataObject {

	/**
	 * The ID if interfacing with a database.
	 */
	private $id = 0;
	
	/**
	 * The primary key if interfacing with a database.
	 */
	private $pkey = NULL;
	
	/**
	 * The name of the table if interfacing with a database.
	 */
	private $table = NULL;
	
	/**
	 * The data associated with this DataObject. The model is a key/value
	 * array with the key's being the field names of the table.
	 */
	private $model = array();
	
	/**
	 * An array of DataObject's that are children of this DataObject.
	 */
	private $children = array();
	
	/**
	 * Whether or not this DataObject has the date_create and date_modify
	 * fields.
	 */
	private $has_date = true;
	
	/**
	 * The root of the table for automatically building the table name.
	 */
	const TABLE_ROOT = '';
	
	/**
	 * Default constructor. Builds a new DataObject.
	 */
	public function __construct() {
		$this->init();
	}
	
	/**
	 * Destructor. Goodbye.
	 */
	public function __destruct() {
		
	}
	
	/**
	 * Magic method to allow get*() and set*() methods to work automatically.
	 * The method expects either 0 or 1 arguments. If 0 arguments, a getMethod()
	 * is assumed, if 1 argument a setMethod($value) is assumed.
	 * @param string $method The name of the method being called.
	 * @param array $argv The argument vector.
	 * @retval mixed Returns the value of a getMethod(), and $this for a setMethod().
	 */
	public function __call($method, $argv) {
		$argc = count($argv);
		
		$k = substr($method, 3);
		$k = strtolower(substr($k, 0, 1)) . substr($k, 1);
		$k = preg_replace('/[A-Z]/', '_\\0', $k);
		$k = strtolower($k);
		
		if ( 0 === $argc ) {
			/* If the length is 0, assume this is a get() */
			$v = $this->__get($k);
			return $v;
		} else {
			$v = current($argv);
			
			/* If the key is the pkey of the object, don't allow that to be set. */
			$pkey = $this->pkey();
			if ( $k == $pkey ) {
				$this->id($v);
			} else {
				/* Else assume its a set with the first element of $argv. */
				$this->__set($k, $v);
			}
			
			return $this;
		}
	}
	
	/**
	 * Magic method for setting data of the model.
	 * @param string $k The name of the key to set.
	 * @param mixed $v The value of the key to set.
	 * @retval bool Returns true;
	 */
	public function __set($k, $v) {
		$this->model[$k] = $v;
		return true;
	}

	/**
	 * Magic method for getting data from the model.
	 * @param string $k The name of the key to get.
	 * @retval mixed Returns the value if found, NULL otherwise.
	 */
	public function __get($k) {
		if ( true === isset($this->model[$k]) ) {
			return $this->model[$k];
		}
		return NULL;
	}
	
	public function exists() {
		$id = $this->id();
		if ( $id > 0 ) {
			return true;
		}
		return false;
	}
	
	/**
	 * ANONYMOUS GETTERS AND SETTERS
	 */
	
	/**
	 * Set or get all of the model data. If this has an array for an argument,
	 * the model will be set. If no argument is passed, the model will be returned.
	 * @param array $model The optional argument to set all of the model data at once.
	 * @retval mixed If no argument, returns the model, if an array argument is present, $this is returned.
	 */
	public function model() {
		$argc = func_num_args();
		if ( 0 === $argc ) {
			return $this->model;
		} else {
			$model = func_get_arg(0);
			if ( false === is_array($model) ) {
				$model = array();
			}
			
			$pkey = $this->pkey();
			if ( true === isset($model[$pkey]) ) {
				$id = $model[$pkey];
				$this->id($id);
				unset($model[$pkey]);
			}
			
			$this->model = $model;
			return $this;
		}
	}
	
	/**
	 * Set or get the ID of this DataObject.
	 * @param integer $id The value of the ID to set.
	 * @retval mixed If no argument, returns the id, if an argument is present, $this is returned.
	 */
	public function id() {
		$argc = func_num_args();
		if ( 0 === $argc ) {
			return $this->id;
		} else {
			$id = func_get_arg(0);
			$this->id = $id;
			return $this;
		}
	}
	
	/**
	 * Set or get the table of this DataObject. This can obviously be ignored if this data object
	 * isn't being used for any database related things.
	 * @param integer $id The value of the ID to set.
	 * @retval mixed If no argument, returns the id, if an argument is present, $this is returned.
	 */
	public function table() {
		$argc = func_num_args();
		if ( 0 === $argc ) {
			return $this->table;
		} else {
			$table = func_get_arg(0);
			$this->table = self::TABLE_ROOT . $table;
			return $this;
		}
	}
	
	/**
	 * Set or get the primary key of this DataObject. This can obviously be ignored if this data object
	 * isn't being used for any database related things.
	 * @param string $pkey The value of the pkey to set.
	 * @retval mixed If no argument, returns the pkey, if an argument is present, $this is returned.
	 */
	public function pkey() {
		$argc = func_num_args();
		if ( 0 === $argc ) {
			return $this->pkey;
		} else {
			$pkey = func_get_arg(0);
			$this->pkey = $pkey;
			return $this;
		}
	}
	
	/**
	 * Set or get the all of the children of this data object.
	 * @param array $children An array of DataObject's that are children to this DataObject.
	 * @retval mixed If no argument, returns the children, if an argument is present, $this is returned.
	 */
	public function children() {
		$argc = func_num_args();
		if ( 0 === $argc ) {
			return $this->children;
		} else {
			$children = func_get_arg(0);
			if ( false === is_array($children) ) {
				$children = array();
			}
			$this->children = $children;
			return $this;
		}
	}

	/**
	 * Set or get whether this DataObject has a date associated with it.
	 * @param bool $has_date True/False whether this DataObject has a date associated with it.
	 * @retval mixed If no argument, returns whether this has a date or not, if an argument is present, $this is returned.
	 */
	public function hasDate() {
		$argc = func_num_args();
		if ( 0 === $argc ) {
			return $this->has_date;
		} else {
			$has_date = func_get_arg(0);
			if ( false !== $has_date && true !== $has_date ) {
				$has_date = true;
			}
			$this->has_date = true;
			return $this;
		}
	}
	
	/**
	 * CLASS MODIFIERS
	 */
	
	/**
	 * Initializes the class to a bunch of empty data.
	 * @retval bool Returns true.
	 */
	public function init() {
		$class_name = strtolower(get_class($this));
		$this->id(0)
			->model(array())
			->children(array())
			->hasDate(true)
			->table($class_name)
			->pkey($class_name . '_id');
		
		return true;
	}
}