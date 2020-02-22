<?php

class Database 
{
	private static $dbHost = ****;
	private static $dbName = ****;
	private static $dbUSer = ****;
	private static $dbUserPassword = ****;

	private static $connection = null;

	public static function connect() {
		try {
			self::$connection = new PDO("mysql:host=" . self::$dbHost . ";dbname=". self::$dbName, self::$dbUSer, self::$dbUserPassword);
		} catch (Exception $e) {
			die($e->getMessage());
		}
		return self::$connection;
	}

	public static function disconnect(){
		self::$connection = null;
	} 
}

Database::connect();
	
?>