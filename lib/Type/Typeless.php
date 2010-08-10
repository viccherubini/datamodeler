<?php

declare(encoding='UTF-8');
namespace DataModeler\Type;

use \DataModeler\Type;

class TypelessType extends Type {
	
	public function setDefault($default) {
		$this->data['default'] = $default;
		return $this;
	}
	
	public function setValue($value) {
		$this->data['value'] = $value;
		return $this;
	}
	
}
