<?php

declare(encoding='UTF-8');
namespace DataModeler;

class Exception extends \Exception {

	public function __construct($message) {
		$messageBits = array();
		
		$trace = $this->getTrace();
		$trace = current($trace);
		
		if ( isset($trace['class']) ) {
			$messageBits[] = $trace['class'];
		}
		
		$function = NULL;
		if ( isset($trace['function']) ) {
			$messageBits[] = $trace['function'];
		}

		$messageBits[] = $message;
		$message = implode('_', $messageBits);
		
		parent::__construct($message);
	}

}