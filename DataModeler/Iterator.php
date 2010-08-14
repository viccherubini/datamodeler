<?php

declare(encoding='UTF-8');
namespace DataModeler;

use DataModeler\Model,
	DataModeler\Iterator;


/**
 * Extends PHP's iteration abilities to provide other functionality. This
 * class takes an array of data (each element can be of any type)
 * and allows fast and easy iteration through it. Additionally, it can
 * filter on the elements of the list and easily paginate through the list.
 * This generally won't work well with a non numerically indexed array.
 * 
 * @author vmc <vmc@leftnode.com>
 * @todo Allow mapping of methods over each element.
 */
class Iterator implements \Iterator {
	
	private $data = array();
	private $key = 0;
	private $length = 0;
	private $filter = array();
	private $filterCount = 0;
	private $limit = -1;
	private $page = 0;
	
	public function __construct(array $data) {
		$this->data = $data;
		$this->key = 0;
		$this->length = count($data);
	}
	
	public function __destruct() {
		$this->data = array();
	}
	
	public function __clone() {
		$this->length = count($this->data);
		$this->rewind();
	}

	public function current() {
		if ( true === isset($this->data[$this->key]) ) {
			return $this->data[$this->key];
		}
		
		return current($this->data);
	}

	public function getData() {
		return $this->data;
	}

	public function rewind() {
		if ( $this->page > 0 && $this->limit > -1 ) {
			$this->key = (($this->page-1) * $this->limit);
			$this->length = ($this->page * $this->limit);
		} else {
			$this->key = 0;
		}

		reset($this->data);
		return $this;
	}
	
	public function last() {
		return end($this->data);
	}
	
	public function key() {
		return $this->key;
	}

	public function next() {
		$this->key++;
		return next($this->data);
	}

	public function valid() {
		return ( $this->key != $this->length && true === isset($this->data[$this->key]) );
	}

	public function page($page) {
		$this->page = abs(intval($page));
		return $this;
	}

	public function limit($limit) {
		$this->limit = intval($limit);
		return $this;
	}

	public function length() {
		return $this->length;
	}
	
	public function fetch() {
		$fetchList = $this->data;
		
		if ( true === $this->hasFilter() ) {
			
			$fetchList = array();
			
			foreach ( $this->data as $iteratorItem ) {
				if ( true === $iteratorItem instanceof \DataModeler\Model ) {
					$model = $iteratorItem->nvp();
				} elseif ( true === is_array($iteratorItem) ) {
					$model = $iteratorItem;
				}
				
				if ( true === $this->applyFilter($model) ) {
					$fetchList[] = $iteratorItem;
				}
			}
			
			if ( $this->limit > 0 ) {
				$fetchList = array_slice($fetchList, 0, $this->limit);
			}
		}

		return (new Iterator($fetchList));
	}
	
	
	public function filter($field, $value) {
		$this->limit = -1;
		$this->filter[] = array($field, $value);
		$this->filterCount++;
		return $this;
	}
	
	
	public function hasFilter() {
		if ( true === is_array($this->filter) && $this->filterCount > 0 ) {
			return true;
		}
		return false;
	}
	
	
	private function applyFilter($dataMap) {
		$passed = false;
		$matchCount = 0;

		foreach ( $this->filter as $filter ) {
			$field = $filter[0];
			$value = $filter[1];

			/* Now $field can look like 'field > ?' or 'field=?', or 'field     <>    ?' */
			$opBits = array();
			$found_match = preg_match('/([a-z0-9-_.]*)[ ]*([=,==,>,>=,<,<=,!=,<>]{1,2})[ ]*([?]{1})/i', $field, $opBits);
			
			$field = trim($opBits[1]);
			$op = trim($opBits[2]);
			
			if ( true == isset($dataMap[$field]) ) {
				switch ( $op ) {
					case '==':
					case '=': {
						if ( $dataMap[$field] == $value ) {
							$matchCount++;
						}
						break;
					}
					
					case '!=':
					case '<>': {
						if ( $dataMap[$field] != $value ) {
							$matchCount++;
						}
						break;
					}
					
					case '>=': {
						if ( $dataMap[$field] >= $value ) {
							$matchCount++;
						}
						break;
					}
					
					case '<=': {
						if ( $dataMap[$field] <= $value ) {
							$matchCount++;
						}
						break;
					}
					
					case '<': {
						if ( $dataMap[$field] < $value ) {
							$matchCount++;
						}
						break;
					}
					
					case '>': {
						if ( $dataMap[$field] > $value ) {
							$matchCount++;
						}
						break;
					}
				}
				
				if ( $matchCount == $this->filterCount ) {
					$passed = true;
				}
			}
		}
		
		return $passed;
	}
}