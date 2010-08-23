<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use \DataModeler\Model;

require_once 'DataModeler/Model.php';

class User extends Model {
	protected $table = 'users';
	
	protected $pkey = 'id';
	
	/** [type INTEGER] */
	public $id = 0;
	
	/** [type STRING] [maxlength 255] */
	public $username = NULL;
	
	/** [type STRING] [maxlength 255] */
	public $password = NULL;
	
	/** [type INTEGER] */
	public $age = 0;
	
	/** [type STRING] [maxlength 255] */
	public $favorite_book = NULL;
}
