<?php

declare(encoding='UTF-8');


class AllTests {
	public static function suite() {
		$suite = new \PHPUnit_Framework_TestSuite('DataModeler Tests');

		return $suite;
	}
}