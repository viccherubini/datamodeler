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
}

