<?php

declare(encoding='UTF-8');
namespace DataModeler;

class Exception extends \Exception {

	public function __construct($message) {
		$msg = NULL;

		$trace = $this->getTrace();
		$traceLength = count($trace);

		$methods = array();
		for ( $i=$traceLength-1; $i>=0; $i-- ) {
			$args = $trace[$i]['args'];
			$argc = count($args);

			$argv = array();
			for ( $j=0; $j<$argc; $j++ ) {
				if ( is_object($args[$j]) ) {
					$argv[] = get_class($args[$j]);
				} elseif ( is_array($args[$j]) ) {
					$argv[] = 'array';
				} elseif ( is_bool($args[$j]) ) {
					$argv[] = ( false === $args[$j] ? 'false' : 'true' );
				} elseif ( is_null($args[$j]) ) {
					$argv[] = 'NULL';
				} else {
					$argv[] = $args[$j];
				}
			}

			$signature = $trace[$i]['class'] . $trace[$i]['type'] . $trace[$i]['function'];
			$arguments = implode(',', $argv);

			$methods[] = '[' . $i . '] ' . $signature . '(' . $arguments . ')';
		}

		$filename = parent::getFile();
		$lineNumber = parent::getLine();

		$methodTrace = implode(PHP_EOL, $methods);
		$msg = $message . PHP_EOL . PHP_EOL . $methodTrace . PHP_EOL . PHP_EOL . $filename . ' +' . $lineNumber;

		parent::__construct($msg);
	}

}