<?php

declare(encoding='UTF-8');
namespace DataModeler\Type;

use \DataModeler\Type;

class TypelessType extends Type {
	
	public function value($v) {
		return $v;
	}
	
}