<?php
include_once "MySqlDatabase.php";

class DBFactory {
	static function getDatabase($kind = 'mysql') {
		$db = null;
		if ($kind == 'mysql') {
			$db = new MySqlDatabase();
		}
		return $db;
	} 
}
?>