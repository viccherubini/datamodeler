<?php

class DataModelerException extends Exception {
	public function __construct($error_message) {
		parent::__construct($error_message);
	}

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