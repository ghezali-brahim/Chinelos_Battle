<?php
if (!defined ('TEST_INCLUDE'))
	die ("Vous n'avez pas accès directement à ce fichier");


class DBMapper
{
	protected static $database;
	static function init ($db)
	{
		self::$database = $db;
	}

}
