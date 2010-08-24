<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

class MiscTest extends TestCase {

	/**
	 * @dataProvider providerDocComment
	 */
	public function testDocCommentExtractor($docComment, $expectedMatches) {
		$docComment = str_replace(array('/**', '*/'), array(NULL, NULL), $docComment);
		$docComment = trim($docComment);
		
		preg_match_all('#\[[a-z]+ [a-z0-9]+\]+#i', $docComment, $foundMatches);
		$foundMatches = current($foundMatches);
		
		$this->assertEquals($expectedMatches, $foundMatches);
	}

	public function testRemovesFirstAndLastCharacters() {
		$string = '[abc]';
		$string = substr(substr($string, 1), 0, strlen($string)-2);
		
		$this->assertEquals('abc', $string);
	}
	
	/**
	 * @dataProvider providerDate
	 */
	public function testDateParse($date, $expected) {
		$dateParsed = date_parse($date);
		$valid = ( count($dateParsed['errors']) > 0 ? false : true );

		$this->assertEquals($expected, $valid);
	}
	
	public function testRegexDate() {
		$value = '2019-12-12';
		
		$matchCount = preg_match('/^(19|20)\d\d-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/', $value);
		
		//var_dump($matchCount);
	}

	public function testRegexDatetime() {
		$value = '2019-12-12 10:00:00';
		
		$matchCount = preg_match('/^(19|20)\d\d-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01]) (0[0-9]|1[0-9]|2[0-3]):(0[0-9]|[1-5][0-9]|60):(0[0-9]|[1-5][0-9]|60)$/', $value);
		
		var_dump($matchCount);
	}


	public function providerDocComment() {
		return array(
			array("/** [type BOOL] [maxlength 50] */", array('[type BOOL]', '[maxlength 50]')),
			array("/**[type BOOL][maxlength 50]*/", array('[type BOOL]', '[maxlength 50]')),
			array("/**[type BOOL]*/", array('[type BOOL]')),
			array("/** normal comment */", array()),
			array("/** [default 100] */", array('[default 100]')),
			array("/**
 * [type BOOL]
 * [maxlength 50]
 */", array('[type BOOL]', '[maxlength 50]')),
			array("/**
							* [type BOOL]
							* [maxlength 50]
							*/", array('[type BOOL]', '[maxlength 50]'))
		);
	}
	
	public function providerDate() {
		return array(
			array('2010-06-10', true),
			array('2010-06-100', false),
			array('2010-32-99', false),
			array('0000-00-00', true),
			array('2009-12-31', true),
			array('2009-12-32', false),
			array('2009-2-2', true),
			array('2009-12-31 00:00:00', true),
			array('2009-12-31 00:00', true)
		);
	}
}