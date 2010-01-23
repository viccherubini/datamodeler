<?php

class DataModel {
	
	protected $data_adapter = NULL;
	
	public function __construct(DataAdapterPdo $data_adapter) {
		$this->setDataAdapter($data_adapter);
	}
	
	public function __destruct() {
		
	}
	
	
	
	
	public function setDataAdapter(DataAdapterPdo $data_adapter) {
		$this->data_adapter = $data_adapter;
		return $this;
	}
	
	
	
	
	public function getDataAdapter() {
		return $this->data_adapter;
	}
	
	
	
	
	
	
	public function load(DataObject $object, $pkey_value=NULL) {
		$this->hasDataAdapter();
		
		$table = $object->table();
		$pkey = $object->pkey();
		
		/**
		 * Attempt to load from data in the object. If there is data
		 * and the pkey exists, use that, otherwise, attempt to load it from
		 * the database.
		 */
		
		$data = $object->model();
		if ( false === isset($data[$pkey]) ) {
			$sql = "SELECT * FROM `" . $table . "` WHERE `" . $pkey . "` = ?";
			$result = $this->getDataAdapter()->query($sql, array($pkey_value));
			if ( 1 === $result->getRowCount() ) {
				$data = $result->fetch();
			}
			
			$object->model($data);
		}
		
		return $object;
	}

	public function save(DataObject $object) {
		$this->hasDataAdapter();

		$id = $object->id();
		if ( $id > 0 ) {
			$id = $this->update($object);
		} else {
			$id = $this->insert($object);
		}
		
		return $id;
	}
	
	/*
	protected function insert(DataObject $object) {
		$date_create = $object->getDateCreate();
		if ( true === $object->getHasDate() && true === empty($date_create) ) {
			$object->setDateCreate(time());
		}
		
		$table = $object->table();
		$pkey = $object->pkey();
		$data = $object->model();
		$data_length = count($data);
		$field_list = implode('`, `', array_keys($data));
		$value_list = implode(', ', array_fill(0, $data_length, '?'));
		
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
	*/
	
	protected function hasDataAdapter() {
		if ( NULL === $this->getDataAdapter() ) {
			throw new DataModelerException('No DataAdapter has been set. Please set one first.');
		}
		return true;
	}
}