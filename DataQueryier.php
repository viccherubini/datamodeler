<?php

require_once 'DataIterator.php';

/**
 * Query data from the database and return a single element or 
 * a DataIterator of all matched elements. This will return
 * a single DataObject object or a DataIterator object.
 * @author vmc <vmc@leftnode.com>
 * @code
 * $queryier = new DataQueryier($model);
 * $dataobj  = $queryier->where('id > ?', $id)->where('name <> ?', $name)->findFirst();
 * $dataiter = $queryier->where('id > ?', $id)->where('name <> ?', $name)->find();
 * @endcode
 */
class DataQueryier {
	private $data_model = NULL;
	private $field_list = array();
	private $where_list = array();
	
	public function __construct(DataModel $data_model) {
		$this->setDataModel($data_model);
		$this->where_list = array();
	}
	
	
	public function __destruct() {
		$this->where_list = array();
	}
	
	
	
	public function setDataModel(DataModel $data_model) {
		$this->data_model = $data_model;
		return $this;
	}
	
	
	public function getDataModel() {
		return $this->data_model;
	}
	
	public function getFieldList() {
		return $this->field_list;
	}
	
	public function getWhereList() {
		return $this->where_list;
	}
	
	
	public function field($field) {
		$this->field_list[] = $field;
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
	
	public function findFirst(DataObject $object) {
		$result_single = $this->executeQuery();
		$data = (array)$result_single->fetch();
		
		$object->set($data);
		return $object;
	}
	
	public function find(DataObject $object) {
		$result_all = $this->executeQuery();
		$result_list = array();
		if ( $result_all->getRowCount() > 0 ) {
			$result_list = $result_all->fetchAll();
			
			$length = count($result_list);
			for ( $i=0; $i<$length; $i++ ) {
				$result_list[$i] = clone $object->set($result_list[$i]);
			}
		}
		
		return (new DataIterator($result_list, $object));
	}
	
	private function buildQuery() {
		$table = $this->getDataModel()->getTable();
		$pkey = $this->getDataModel()->getPkey();
		
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
		
		return $sql;
	}
	
	private function executeQuery() {
		$sql = $this->buildQuery();
		
		$where_list = $this->getWhereList();
		$result = $this->getDataModel()
			->getDataAdapter()
			->query($sql, array_values($where_list));
			
		return $result;
	}
}