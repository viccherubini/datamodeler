<?php

/**
 * Small class to extend the usefulness of an exception.
 * @author vmc <vmc@leftnode.com>
 */
class DataModelerException extends Exception {
	
	/**
	 * Help! I've died.
	 * @param string $error_message The message to report.
	 */
	public function __construct($error_message) {
		parent::__construct($error_message);
	}

	/**
	 * Nicely prints the error, including the class, method, file and line in a vim 
	 * standard format so you can copy and paste the file, open it in vim, and
	 * the error will be there.
	 * @retval string The nicely printed error string.
	 */
	public function __toString() {
		$trace = parent::getTrace();
		$trace = current($trace);

		$error_class = NULL;
		if ( true === isset($trace['class']) ) {
			$class = $trace['class'];
			$error_class = $class . $trace['type'];
		}
		
		if ( true === isset($trace['function']) ) {
			$function = $trace['function'];
			$error_class .= $function . '() > ';
		}
		
		$error_file = $this->getFile() . ' +' . $this->getLine();
		$error_code = $this->getMessage() . ' (' . $error_file . ')';
		
		return $error_class . $error_code;
	}
}