<?php

abstract class DataModel {
	
	protected $data_adapter = NULL;
	
	protected $pkey = NULL;
	protected $table = NULL;
	
	const TABLE_ROOT = '';
	
	public function __construct(DataAdapterPdo $data_adapter) {
		$this->setDataAdapter($data_adapter);
		$this->init();
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
	
	public function setDataAdapter(DataAdapterPdo $data_adapter) {
		$this->data_adapter = $data_adapter;
		return $this;
	}
	
	
	
	
	public function getDataAdapter() {
		return $this->data_adapter;
	}
	
	public function getPkey() {
		return $this->pkey;
	}
	
	public function getTable() {
		return $this->table;
	}
	
	
	
	
	
	public function load(DataObject &$object, $pkey_value) {
		$this->hasDataAdapter();
		
		$table  = $this->getTable();
		$pkey   = $this->getPkey();
		$sql    = "SELECT * FROM `" . $table . "` WHERE `" . $pkey . "` = ?";
		$result = $this->getDataAdapter()->query($sql, array($pkey_value));
		if ( 1 === $result->getRowCount() ) {
			$data = $result->fetch();

			if ( true === isset($data[$pkey]) ) {
				$id = $data[$pkey];
				unset($data[$pkey]);
			}
			
			$object->setId($id)->setObjectData($data);
		}
		
		return $object;
	}
	
	public function save(DataObject $object) {
		$this->hasDataAdapter();
		
		$id = $object->getId();
		if ( $id > 0 ) {
			$id = $this->update($object);
		} else {
			$id = $this->insert($object);
		}
		
		return $id;
	}
	
	protected function insert(DataObject $object) {
		$date_create = $object->getDateCreate();
		if ( true === $object->getHasDate() && true === empty($date_create) ) {
			$object->setDateCreate(time());
		}
		
		$table = $this->getTable();
		$data = $object->get();
		$pkey = $object->getPkey();
		$data_length = count($data);
		$field_list = implode('`, `', array_keys($data));
		$value_list = implode(', ', array_fill(0, $data_length, '?'));
		
		if ( true === isset($data[$pkey]) ) {
			unset($data[$pkey]);
		}
		
		$sql = "INSERT INTO `" . $table . "` (`" . $field_list . "`) VALUES(" . $value_list . ")";
		$result = $this->getDataAdapter()->query($sql, array_values($data));

		$id = 0;
		if ( 1 === $result->getRowCount() ) {
			$id = $this->getDataAdapter()->insertId();
		}
		
		return $id;
	}
	
	protected function update(DataObject $object) {
		$date_modify = $object->getDateModify();
		if ( true === $object->getHasDate() && true === empty($date_modify) ) {
			$object->setDateModify(time());
		}
		
		$i = 1;
		$field_list = NULL;
		$id = $object->getId();
		$data = $object->get();
		$table = $this->getTable();
		$pkey = $this->getPkey();
		$length = count($data);
		
		foreach ( $data as $field => $value ) {
			$field_list .= "`" . $field . "` = ?";
			if ( $i++ != $length ) {
				$field_list .= ', ';
			}
		}
		
		$sql = "UPDATE `" . $table . "` SET " . $field_list . " WHERE `" . $pkey . "` = '" . $id . "'";
		$result = $this->getDataAdapter()->query($sql, array_values($data));
		
		$id = 0;
		if ( 1 === $result->getRowCount() ) {
			$id = $object->getId();
		}
		
		return $id;
	}
	
	protected function hasDataAdapter() {
		if ( NULL === $this->getDataAdapter() ) {
			throw new DataModelerException('No DataAdapter has been set. Please set one first.');
		}
		return true;
	}


	abstract protected function init();
}