<?php

abstract class DataModel {
	protected $adapter = NULL;
	
	protected $pkey = NULL;
	protected $table = NULL;
	
	const TABLE_ROOT = '';
	
	public function __construct(DataAdapter $adapter) {
		
	}
	
	public function __destruct() {
		
	}
	
	
	
	
	public function setPkey($pkey) {
		$this->pkey = $pkey;
		return $this;
	}
	
	public function setTable($table) {
		$this->table = $table;
		return $this;
	}
	
	public function setDataAdapter(DataAdapter $adapter) {
		$this->adapter = $adapter;
		return $this;
	}
	
	
	
	
	
	public function getPkey() {
		return $this->pkey;
	}
	
	public function getTable() {
		return $this->table;
	}
	
	
	
	
	
	
	public function load(DataObject $object, $pkey) {
		$this->hasDataAdapter();
	}
	
	public function save(DataObject $object) {
		$this->hasDataAdapter();
		
		$id = $object->getId();
		if ( $id > 0 ) {
			$id = $this->insert($object);
		} else {
			$id = $this->update($object);
		}
		
		return $id;
	}
	
	protected function insert(DataObject $object) {
		$date_create = $object->getDateCreate();
		if ( true === $object->getHasDate() && true === empty($date_create) ) {
			$object->setDateCreate(time());
		}
		
		$this->adapter->insert()
			->into($this->getTable())
			->values($object->get())
			->query();

		$id = 0;
		if ( 1 == $this->adapter->affectedRows() ) {
			$id = $this->adapter->insertId();
		}
		
		return $id;
	}
	
	protected function update(DataObject $object) {
		$date_modify = $object->getDateModify();
		if ( true === $object->getHasDate() && true === empty($date_modify) ) {
			$object->setDateModify(time());
		}
		
		$this->adapter->update()
			->table($this->getTable())
			->set($object->get())
			->where($this->getPkey() . ' = ?', $object->getId())
			->query();
		
		$id = $object->getId();
		return $id;
	}
	
	protected function hasDataAdapter() {
		if ( NULL === $this->adapter ) {
			throw new Exception('No DataAdapter has been set. Please set one first.');
		}
		return true;
	}
	
	protected function init(DataObject $object) {
		$class = strtolower(get_class($object));
		
		$this->setTable(self::TABLE_ROOT . $class);
		$this->setPkey($class . '_id');
	}
}
