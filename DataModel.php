<?php

class DataModel {
	
	private $data_adapter = NULL;
	private $field_list = array();
	private $where_list = array();
	private $groupby_list = array();
	private $limit = -1;
	private $orderby = NULL;
	
	/**
	 * LOAD_FIRST will return a DataObject regardless of how many rows are found.
	 */
	const LOAD_FIRST = 4;
	
	/**
	 * LOAD_ALL will return an iterator regardless of how many rows are found.
	 */
	const LOAD_ALL = 8;
	
	
	public function __construct(DataAdapterPdo $data_adapter) {
		$this->setDataAdapter($data_adapter);
	}
	
	public function __destruct() {
		
	}
	
	
	
	
	public function setDataAdapter(DataAdapterPdo $data_adapter) {
		$this->data_adapter = $data_adapter;
		return $this;
	}
	
	public function setFieldList(array $field_list) {
		$this->field_list = $field_list;
		return $this;
	}
	
	public function setWhereList(array $where_list) {
		$this->where_list = $where_list;
		return $this;
	}
	
	public function setGroupByList(array $groupby_list) {
		$this->groupby_list = $groupby_list;
		return $this;
	}
	
	public function setLimit($limit) {
		$limit = intval($limit);
		$this->limit = $limit;
		return $this;
	}
	
	public function setOrderBy($orderby) {
		$this->orderby = $orderby;
		return $this;
	}
	
	
	
	
	
	
	
	
	public function getDataAdapter() {
		return $this->data_adapter;
	}
	
	public function getFieldList() {
		return $this->field_list;
	}
	
	public function getWhereList() {
		return $this->where_list;
	}
	
	public function getGroupByList() {
		return $this->groupby_list;
	}
	
	public function getLimit() {
		return $this->limit;
	}
	
	public function getOrderBy() {
		return $this->orderby;
	}
	
	
	
	
	
	public function field($field) {
		if ( false === in_array($field, $this->field_list) ) {
			$this->field_list[] = $field;
		}
		return $this;
	}
	
	
	public function where($field, $value) {
		/* Now $field can look like 'field > ?' or 'field=?', or 'field     <>    ?' */
		$match_list = array();
		$found_match = preg_match('/([a-z0-9-_.]*)[ ]*([=,==,>,>=,<,<=,!=,<>]{1,2})[ ]*([?]{1})/i', $field, $match_list);
		
		if ( 0 !== $found_match ) {
			$field = trim(@$match_list[1]);
			$operator = trim(@$match_list[2]);
		
			$field_and_operator = '`' . $field . '` ' . $operator . ' ?';
		
			$this->where_list[$field_and_operator] = $value;
		}
		
		return $this;
	}	
	
	
	public function limit($limit) {
		$limit = intval($limit);
		if ( $limit > 0 ) {
			$this->setLimit($limit);
		}
		
		return $this;
	}
	
	
	public function orderBy($orderby) {
		$this->setOrderBy($orderby);
		return $this;
	}
	
	public function groupBy($groupby) {
		if ( false === in_array($groupby, $this->groupby_list) ) {
			$this->groupby_list[] = $groupby;
		}
		
		return $this;
	}
	
	
	public function loadFirst(DataObject $object) {
		$this->hasDataAdapter();
		
		$db_result = $this->load($object);
		
		if ( $db_result->getRowCount() > 0 ) {
			$model = $db_result->fetch();
			$db_result->free();
			$object->model($model);
		}
		
		return $object;
		
	}
	
	public function loadAll(DataObject $object) {
		$this->hasDataAdapter();
		
		$db_result = $this->load($object);
		
		$result_list = array();
		if ( $db_result->getRowCount() > 0 ) {
			$model_list = $db_result->fetchAll();
			$model_length = count($model_list);
			
			for ( $i=0; $i<$model_length; $i++ ) {
				$result_list[$i] = clone $object->model($model_list[$i]);
			}
		}
		
		$db_result->free();
		
		$object_iterator = new DataIterator($result_list);
		
		return $object_iterator;
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
	
	
	protected function insert(DataObject $object) {
		$date_create = $object->getDateCreate();
		if ( true === $object->hasDate() && true === empty($date_create) ) {
			$object->setDateCreate(time());
		}
		
		$table = $object->table();
		$pkey = $object->pkey();
		$model = $object->model();
		$model_length = count($model);
		
		$field_list = implode('`, `', array_keys($model));
		$value_list = implode(', ', array_fill(0, $model_length, '?'));
		
		$sql = "INSERT INTO `" . $table . "` (`" . $field_list . "`) VALUES(" . $value_list . ")";
		$result = $this->getDataAdapter()->query($sql, array_values($model));

		$id = 0;
		if ( 1 === $result->getRowCount() ) {
			$id = $this->getDataAdapter()->insertId();
		}
		
		return $id;
	}
	
	
	
	protected function update(DataObject $object) {
		$date_modify = $object->getDateModify();
		if ( true === $object->hasDate() && true === empty($date_modify) ) {
			$object->setDateModify(time());
		}
		
		$i = 1;
		$field_list = NULL;
		$id = $object->id();
		$table = $object->table();
		$pkey = $object->pkey();
		$model = $object->model();
		$model_length = count($model);
		
		foreach ( $model as $field => $value ) {
			$field_list .= "`" . $field . "` = ?";
			if ( $i++ != $model_length ) {
				$field_list .= ', ';
			}
		}
		
		$sql = "UPDATE `" . $table . "` SET " . $field_list . " WHERE `" . $pkey . "` = '" . $id . "'";
		$result = $this->getDataAdapter()->query($sql, array_values($model));
		
		$id = 0;
		if ( 1 === $result->getRowCount() ) {
			$id = $object->id();
		}
		
		return $id;
	}

	
	private function hasDataAdapter() {
		if ( NULL === $this->getDataAdapter() ) {
			throw new DataModelerException('No DataAdapter has been set. Please set one first.');
		}
		return true;
	}
	
	
	private function load(DataObject $object) {
		$table = $object->table();
		$pkey = $object->pkey();

		$sql_field_list = NULL;
		$field_list = $this->getFieldList();
		
		if ( 0 === count($field_list) ) {
			$sql_field_list = '*';
		} else {
			$sql_field_list = implode('`, `', $field_list);
			$sql_field_list = '`' . $sql_field_list . '`';
		}
		
		$sql = 'SELECT ' . $sql_field_list . ' FROM `' . $table . '` ';
		
		$value_list = array();
		$where_list = $this->getWhereList();
		if ( count($where_list) > 0 ) {
			$i = 0;
			$sql .= 'WHERE ';
			
			foreach ( $where_list as $field_and_operator => $value ) {
				$sql .= ( $i++ !== 0 ? ' AND ' : NULL ) . '(' . $field_and_operator . ')';
			}
		}
		
		$groupby_list = $this->getGroupByList();
		if ( count($groupby_list) > 0 ) {
			$sql .= ' GROUP BY `' . implode('`, `', $field_list) . '`';
		}
		
		//$orderby = $this->getOrderBy();
		//if ( false === empty($orderby) ) {
		//	$sql .= ' ORDER BY ' . $orderby . '';
		//}
		
		$limit = $this->getLimit();
		if ( $limit > 0 ) {
			$sql .= ' LIMIT ' . $limit;
		}


exit($sql . PHP_EOL . PHP_EOL);

		$result_model = $this->getDataAdapter()->query($sql, array_values($where_list));
		
		$this->setWhereList(array())
			->setFieldList(array())
			->setLimit(-1)
			->setOrderBy(NULL)
			->setGroupByList(array());
		
		return $result_model;
	}
	
	
}