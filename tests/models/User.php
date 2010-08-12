<?php

declare(encoding='UTF-8');
namespace DataModelerTest;

use \DataModeler\Model;

require_once 'lib/Model.php';

class User extends Model {
	protected $table = 'users';
	
	protected $pkey = 'id';
	
	/** [type INTEGER] */
	private $id = 0;
	
	/** [type STRING] [maxlength 255] */
	private $username = NULL;
	
	/** [type STRING] [maxlength 255] */
	private $password = NULL;
	
	/** [type INTEGER] */
	private $age = 0;
	
	/** [type STRING] [maxlength 255] */
	private $favorite_book = NULL;
}