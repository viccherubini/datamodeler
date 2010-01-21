<?php

class DataIterator implements Iterator {
	private $data_list = array();
	private $key = 0;
	private $length = 0;
	private $filter = array();
	private $filter_count = 0;
	private $limit = -1;
	private $page = 0;
	
	private $data_object = NULL;
	
	public function __construct(array $data_list, DataObject $data_object) {
		$this->data_list = $data_list;
		$this->key = 0;
		$this->length = count($data_list);
		$this->data_object = $data_object;
	}
	
	public function __clone() {
		$this->length = count($this->data_list);
		$this->rewind();
	}
	
	public function getList() {
		return $this->data_list;
	}
	
	public function getObject() {
		return $this->data_object;
	}
	
	public function rewind() {
		if ( $this->page > 0 && $this->limit > -1 ) {
			$this->key = ( ($this->page-1) * $this->limit );
			$this->length = ($this->page * $this->limit );
		} else {
			$this->key = 0;
		}

		reset($this->data_list);
	}
	
	public function current() {
		if ( true === isset($this->data_list[$this->key]) ) {
			return $this->data_list[$this->key];
		}
		
		return current($this->data_list);
	}
	
	public function last() {
		return end($this->data_list);
	}
	
	public function key() {
		return $this->key;
	}
	
	public function next() {
		$this->key++;
		return next($this->data_list);
	}
	
	public function valid() {
		return ( $this->key != $this->length && true === isset($this->data_list[$this->key]) );
	}

	public function page($page) {
		$this->page = intval($page);
		return $this;
	}

	public function limit($limit) {
		$this->limit = $limit;
		return $this;
	}

	public function length() {
		return $this->length;
	}
	
	public function fetch() {
		$result_list = $this->data_list;
		
		if ( true === $this->hasFilter() ) {
			$result_list = array();
			foreach ( $this->data_list as $list_item ) {
				if ( true === $this->applyFilter($list_item->get()) ) {
					$result_list[] = $list_item;
				}
			}

			$this->filter = array();
			$this->filter_count = 0;
		}

		return (new DataIterator($result_list, $this->getObject()));
	}
	
	public function filter($field, $value) {
		$this->limit = -1;
		$this->filter[] = array($field, $value);
		$this->filter_count++;
		return $this;
	}
	
	private function hasFilter() {
		if ( true === is_array($this->filter) && $this->filter_count > 0 ) {
			return true;
		}
		return false;
	}
	
	private function applyFilter($data_array) {
		if ( false === $this->hasFilter() ) {
			return true;
		}
		
		$passed = false;
		$match_count = 0;

		foreach ( $this->filter as $filter ) {
			$field = $filter[0];
			$value = $filter[1];

			/* Now $field can look like 'field > ?' or 'field=?', or 'field     <>    ?' */
			$op_bits = array();
			$found_match = preg_match('/([a-z0-9-_.]*)[ ]*([=,==,>,>=,<,<=,!=,<>]{1,2})[ ]*([?]{1})/i', $field, $op_bits);
			
			$field = trim($op_bits[1]);
			$op = trim($op_bits[2]);
			
			if ( true == isset($data_array[$field]) ) {
				switch ( $op ) {
					case '==':
					case '=': {
						if ( $data_array[$field] == $value ) {
							$match_count++;
						}						
						break;
					}
					
					case '!=':
					case '<>': {
						if ( $data_array[$field] != $value ) {
							$match_count++;
						}
						break;
					}
					
					case '>=': {
						if ( $data_array[$field] >= $value ) {
							$match_count++;
						}
						break;
					}
					
					case '<=': {
						if ( $data_array[$field] <= $value ) {
							$match_count++;
						}
						break;
					}
					
					case '<': {
						if ( $data_array[$field] < $value ) {
							$match_count++;
						}
						break;
					}
					
					case '>': {
						if ( $data_array[$field] > $value ) {
							$match_count++;
						}
						break;
					}
				}
				
				if ( $match_count == $this->filter_count ) {
					$passed = true;
				}
			}
		}
		
		return $passed;
	}
}