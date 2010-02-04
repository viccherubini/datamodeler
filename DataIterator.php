<?php

/**
 * Extends PHP's iteration abilities to provide other functionality. This
 * class takes an array of data (each element can be of any type)
 * and allows fast and easy iteration through it. Additionally, it can
 * filter on the elements of the list and easily paginate through the list.
 * This generally won't work well with a non numerically indexed array.
 * @author vmc <vmc@leftnode.com>
 * @todo Allow mapping of methods over each element.
 */
class DataIterator implements Iterator {
	
	/**
	 * The list of data for this iterator.
	 */
	private $data_list = array();
	
	/**
	 * The key of the current element of the iterator.
	 */
	private $key = 0;
	
	/**
	 * The overall length of the data in the iterator.
	 */
	private $length = 0;
	
	/**
	 * An array of arrays to filter each element by.
	 */
	private $filter = array();
	
	/**
	 * The number of filters. Provides a quick way to see if the filters are set or not.
	 */
	private $filter_count = 0;
	
	/**
	 * The number to limit the results by, ignored if -1, all positive values are used.
	 */
	private $limit = -1;
	
	/**
	 * The current page to go to.
	 */
	private $page = 0;
	
	
	/**
	 * Build me.
	 * @param array $data_list The array of data to load into the iterator.
	 */
	public function __construct(array $data_list) {
		$this->data_list = $data_list;
		$this->key = 0;
		$this->length = count($data_list);
	}
	
	/**
	 * Allow a clean clone of this object.
	 */
	public function __clone() {
		$this->length = count($this->data_list);
		$this->rewind();
	}
	
	/**
	 * Returns the data list.
	 * @retval array The data list.
	 */
	public function getDataList() {
		return $this->data_list;
	}
	
	/**
	 * Rewind to the beginning of the data list, or if there is a page and 
	 * limit set, rewind to that specific area. So, if you want to show 10 elements
	 * from page 4, the key would be 30 and the length would be 40.
	 */
	public function rewind() {
		if ( $this->page > 0 && $this->limit > -1 ) {
			$this->key = (($this->page-1) * $this->limit);
			$this->length = ($this->page * $this->limit);
		} else {
			$this->key = 0;
		}

		reset($this->data_list);
	}
	
	/**
	 * Get current element from the data list if it exists. Note, this does
	 * not necessarily mean the HEAD element of the list, if the key has been
	 * maniuplated in some sort, current() will return that element.
	 * @retval mixed Returns the current element, or if a key is set, returns that element.
	 */
	public function current() {
		if ( true === isset($this->data_list[$this->key]) ) {
			return $this->data_list[$this->key];
		}
		
		return current($this->data_list);
	}
	
	/**
	 * Returns the last element of the data list, always.
	 * @retval mixed Returns the last element of the data list, always.
	 */
	public function last() {
		return end($this->data_list);
	}
	
	/**
	 * Gets the current key.
	 * @retval integer The current key set.
	 */
	public function key() {
		return $this->key;
	}
	
	/**
	 * Jumps to the next element of the data list and returns it.
	 * @retval mixed The next element of the data list.
	 */
	public function next() {
		$this->key++;
		return next($this->data_list);
	}
	
	/**
	 * Determines if the current key is valid. It must not be greater than the
	 * length and must be present in the data list.
	 * @retval bool Returns true if it's valid, false otherwise.
	 */
	public function valid() {
		return ( $this->key != $this->length && true === isset($this->data_list[$this->key]) );
	}

	/**
	 * Jump to a specific "page" of the data. 
	 * @param integer $page The page to jump to.
	 * @retval DataIterator This for chaining.
	 */
	public function page($page) {
		$this->page = abs(intval($page));
		return $this;
	}

	/**
	 * Limits the number of results per page.
	 * @param integer $limit The number of results per page.
	 * @retval DataIterator This for chaining.
	 */
	public function limit($limit) {
		$this->limit = intval($limit);
		return $this;
	}

	/**
	 * Returns the length. Note this will be adjusted if page() and limit()
	 * are set. If you set limit to 10, and page to 4, the length would be 40.
	 * @retval integer Returns the length of the data list.
	 */
	public function length() {
		return $this->length;
	}
	
	/**
	 * Fetch all of the elements of the data list. If any filters are set,
	 * the filters will be applied to each of the elements, and if the element
	 * passes the filter, it will be in the new data list.
	 * @retval DataIterator Returns a new data iterator with all of the matching elements.
	 */
	public function fetch() {
		$result_list = $this->data_list;
		
		if ( true === $this->hasFilter() ) {
			$result_list = array();
			foreach ( $this->data_list as $list_item ) {
				if ( true === $list_item instanceof DataObject ) {
					$model = $list_item->model();
				} elseif ( true === is_array($list_item) ) {
					$model = $list_item;
				} else {
					$model = array();
				}
				
				if ( true === $this->applyFilter($model) ) {
					$result_list[] = $list_item;
				}
			}
			
			if ( $this->limit > 0 ) {
				$result_list = array_slice($result_list, 0, $this->limit);
			}
		}

		return (new DataIterator($result_list));
	}
	
	/**
	 * Adds a new filter to match against.
	 * @param string $field The field to filter by. Format of 'field > ?', where the operator can be
	 * one of =, ==, >, >=, <, <=, !=, or <>.
	 * @param mixed $value The value to filter by.
	 * @retval DataIterator This for chaining.
	 */
	public function filter($field, $value) {
		$this->limit = -1;
		$this->filter[] = array($field, $value);
		$this->filter_count++;
		return $this;
	}
	
	/**
	 * Determines if this iterator has a filter or not.
	 * @retval bool Returns true if this iterator has a filter, false otherwise.
	 */
	private function hasFilter() {
		if ( true === is_array($this->filter) && $this->filter_count > 0 ) {
			return true;
		}
		return false;
	}
	
	/**
	 * Apply the filters to each of the elements.
	 * @param array $data_array The list of data to test. Should be a key value array.
	 * @retval bool Returns true if there are no filters, true if all filters passed, or false otherwise.
	 */
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