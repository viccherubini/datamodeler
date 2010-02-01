<?php

/**
 * Ties a DataObject object to a data store. This abstracts the 
 * searching and saving of a DataObject away from the data store.
 * Right now it's tied to a SQL based data store, but future revisions
 * will allow non SQL related ones.
 * @author vmc <vmc@leftnode.com>
 * @todo Move the loading of DataObject data from here to the DataAdapter classes.
 */
class DataModel {
	
	/**
	 * The DataAdapterPdo object for interacting with a database.
	 */
	private $data_adapter = NULL;
	
	/**
	 * The list of fields to return in the query.
	 */
	private $field_list = array();
	
	/**
	 * The list of fields to select WHERE from.
	 */
	private $where_list = array();
	
	/**
	 * The list of fields to GROUP BY.
	 */
	private $groupby_list = array();
	
	/**
	 * The number of rows to limit by. If -1, this is ignored.
	 */
	private $limit = -1;
	
	/**
	 * The field to order the results by.
	 */
	private $orderby_field = NULL;
	
	/**
	 * The direction to order the results by. Must be ASC or DESC.
	 */
	private $orderby_order = NULL;
	
	/**
	 * Constructor. 
	 * @param DataAdapterPdo $data_adapter The data store adapter to write to a database.
	 */
	public function __construct(DataAdapter $data_adapter) {
		$this->setDataAdapter($data_adapter);
	}
	
	/**
	 * Good bye.
	 */
	public function __destruct() {
		
	}
	
	/**
	 * Sets the DataAdapter so database access can be had.
	 * @param DataAdapter $data_adapter The data store adapter to write to a database.
	 * @retval DataModel Returns this for chaining.
	 */
	public function setDataAdapter(DataAdapter $data_adapter) {
		$this->data_adapter = $data_adapter;
		return $this;
	}
	
	/**
	 * Sets a list of fields that should be returned in the query.
	 * @param array $field_list The list of fields to use in the query.
	 * @retval DataModel Returns this for chaining.
	 */
	public function setFieldList(array $field_list) {
		$this->field_list = $field_list;
		return $this;
	}
	
	/**
	 * Sets the list of WHERE arguments.
	 * @param array $where_list The list of WHERE clauses, such as 'field > ?', or 'name <> ?'
	 * @retval DataModel Returns this for chaining.
	 */
	public function setWhereList(array $where_list) {
		$this->where_list = $where_list;
		return $this;
	}
	
	/**
	 * Sets the list of GROUP BY arguments.
	 * @param array $groupby_list The list of GROUP BY clauses, this is simply an array of field names.
	 * @retval DataModel Returns this for chaining.
	 */
	public function setGroupByList(array $groupby_list) {
		$this->groupby_list = $groupby_list;
		return $this;
	}
	
	/**
	 * Sets the limit in LIMIT $value. No business logic is in here, that is in limit(), so the
	 * limit value can be reset to -1. If -1, no LIMIT is used, otherwise, the LIMIT value is used.
	 * @param integer $limit The max number of records to return.
	 * @retval DataModel Returns this for chaining.
	 */
	public function setLimit($limit) {
		$limit = intval($limit);
		$this->limit = $limit;
		return $this;
	}
	
	/**
	 * Sets the ORDER BY field. This only allows a single field for now.
	 * @param string $orderby_field The field to sort the results by.
	 * @retval DataModel Returns this for chaining.
	 */
	public function setOrderByField($orderby_field) {
		$this->orderby_field = $orderby_field;
		return $this;
	}
	
	/**
	 * Sets the direction to order the fields. No business logic is done here, only
	 * done in orderBy() so this can be reset to NULL for further queries.
	 * @param string $orderby_order The direction to order the fields.
	 * @retval DataModel Returns this for chaining.
	 */
	public function setOrderByOrder($orderby_order) {
		$this->orderby_order = $orderby_order;
		return $this;
	}
	
	/**
	 * Returns the data adapter currently being used.
	 * @retval DataAdapter Returns the instance of the DataAdapter that's being used.
	 */
	public function getDataAdapter() {
		return $this->data_adapter;
	}
	
	/**
	 * Returns the data adapter currently being used.
	 * @retval array The list of fields to be returned in the query.
	 */
	public function getFieldList() {
		return $this->field_list;
	}
	
	/**
	 * Returns the list of WHERE fields and their comparisons.
	 * @retval array Returns the list of WHERE fields and their comparisons.
	 */
	public function getWhereList() {
		return $this->where_list;
	}
	
	/**
	 * Returns the list of GROUP BY fields.
	 * @retval array Returns the list of GROUP BY fields.
	 */
	public function getGroupByList() {
		return $this->groupby_list;
	}
	
	/**
	 * Returns the limit.
	 * @retval integer Returns the limit.
	 */
	public function getLimit() {
		return $this->limit;
	}
	
	/**
	 * Returns the field to order by.
	 * @retval string Returns the field to order by.
	 */
	public function getOrderByField() {
		return $this->orderby_field;
	}
	
	/**
	 * Returns the order the ORDER BY field should use.
	 * @retval DataAdapterPdo Returns the order the ORDER BY field should use.
	 */
	public function getOrderByOrder() {
		return $this->orderby_order;
	}
	
	/**
	 * Sets all of the fields that should be returned in a query. This accepts
	 * N number of arguments, and each time this is called, the fields are 
	 * reset to whatever the arguments are. If no arguments are passed, nothing
	 * happens.
	 * @param string Each argument must be a string of the field to include in the result set.
	 * @retval DataModel Returns this for chaining.
	 */
	public function field() {
		$argc = func_num_args();
		$argv = func_get_args();
		
		if ( $argc > 0 ) {
			$this->field_list = $argv;
		}
		
		return $this;
	}
	
	/**
	 * Adds another WHERE clause (or appends to current ones) to the query.
	 * @param string $field A field in format 'field_name > ?'. The operator can be
	 * =, ==, >, >=, <, <=, !=, or <>. The ? will be replaced by $value.
	 * @param string $value The value to replace the ? by in the $field.
	 * @retval DataModel Returns this for chaining.
	 */
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
	
	/**
	 * Sets the limit when doing a SELECT query. Argument must be greater than -1.
	 * @param integer $limit The limit to set.
	 * @retval DataModel Returns this for chaining.
	 */
	public function limit($limit) {
		$limit = intval($limit);
		if ( $limit > 0 ) {
			$this->setLimit($limit);
		}
		
		return $this;
	}
	
	/**
	 * Sets the order by field and the order to do it in.
	 * @param string $field The field to order by.
	 * @param string $order The order to sort by. Must be DESC or ASC.
	 * @retval DataModel Returns this for chaining.
	 */
	public function orderBy($field, $order) {
		$order = strtoupper($order);
		if ( 'ASC' !== $order && 'DESC' !== $order ) {
			$order = 'ASC';
		}
		
		$this->setOrderByField($field)->setOrderByOrder($order);
		return $this;
	}
	
	/**
	 * Sets the order by field and the order to do it in.
	 * @param string $field The field to order by.
	 * @param string $order The order to sort by. Must be DESC or ASC.
	 * @retval DataModel Returns this for chaining.
	 */
	public function groupBy($groupby) {
		if ( false === in_array($groupby, $this->groupby_list) ) {
			$this->groupby_list[] = $groupby;
		}
		
		return $this;
	}
	
	/**
	 * Executes the query and loads the first row found.
	 * @param DataObject $object The object to load the data into. If no row is found, this object
	 * is still returned to always have a consistent return type.
	 * @retval DataObject Returns the object found.
	 */
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
	
	/**
	 * Executes the query and loads all results found. Returns everything as a DataIterator.
	 * Even if no rows are found, a DataIterator is still returned.
	 * @param DataObject $object The object to load the data into for each iterator element.
	 * @retval DataIterator Returns a DataIterator to loop through. Each element is a DataObject element.
	 */
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
	
	/**
	 * Saves a DataObject object in the database through the DataAdapter. If the
	 * DataObject already exists (it knows it has an ID set), the data is updated
	 * otherwise, it is inserted. The parameter is returned by reference so 
	 * you can tell if it exists or not.
	 * @param DataObject $object The object to save in the database.
	 * @retval integer Returns the ID of the inserted or updated DataObject.
	 */
	public function save(DataObject &$object) {
		$this->hasDataAdapter();

		$id = $object->id();
		if ( $id > 0 ) {
			$id = $this->getDataAdapter()->update($object);
		} else {
			$id = $this->getDataAdapter()->insert($object);
		}
		
		$object->id($id);

		return $id;
	}
	
	/**
	 * Deletes a DataObject from the data store.
	 * @param DataObject $object The object to delete. Should contain all info it needs to delete itself.
	 * @retval bool Always returns true.
	 */
	public function delete(DataObject $object) {
		$this->getDataAdapter()->delete($object);
		return true;
	}
	
	/**
	 * Determines if a DataAdapterPdo has been set.
	 * @throw DataModelerException Throws an exception if no DataAdapterPdo has been set.
	 * @retval bool Returns true if the data adapater has been set.
	 */
	private function hasDataAdapter() {
		if ( NULL === $this->getDataAdapter() ) {
			throw new DataModelerException('No DataAdapter has been set. Please set one first.');
		}
		return true;
	}
	
	/**
	 * Loads the data from the database after a findFirst() or findAll() method is called.
	 * @param DataObject $object The DataObject is used to get the name of the table and primary key.
	 * @retval DataAdapterPdoResult Returns the result from the database after a query.
	 */
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
		
		$orderby_field = $this->getOrderByField();
		$orderby_order = $this->getOrderByOrder();
		if ( false === empty($orderby_field) && false === empty($orderby_order) ) {
			$sql .= ' ORDER BY `' . $orderby_field . '` ' . $orderby_order;
		}
		
		$limit = $this->getLimit();
		if ( $limit > 0 ) {
			$sql .= ' LIMIT ' . $limit;
		}

		$result_model = $this->getDataAdapter()->query($sql, array_values($where_list));
		
		$this->setWhereList(array())
			->setFieldList(array())
			->setLimit(-1)
			->setOrderByField(NULL)
			->setOrderByOrder(NULL)
			->setGroupByList(array());
		
		return $result_model;
	}
}
