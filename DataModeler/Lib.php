<?php

declare(encoding='UTF-8');
namespace DataModeler;

function is_scalar_array($array) {
	if ( !is_array($array) ) {
		return false;
	}
	
	if ( 0 == count($array) ) {
		return false;
	}
	
	$isScalar = true;
	foreach ( $array as $v ) {
		if ( !is_scalar($v) ) {
			$isScalar = false;
		}
	}
	
	return $isScalar;
}

function object_to_array($object) {
	if ( !is_object($object) ) {
		return array();
	}
	
	$array = array();
	foreach ( $object as $k => $v ) {
		$array[$k] = $v;
	}
	
	return $array;
}