<?php
include_once 'IDatabase.php';

class MySqlDatabase implements IDatabase {
	
	private $db = FALSE;
	
	function MysSqlDatabase() {
	}
	
	function getPassword($username) {
		$rslt = '';
		$sql = sprintf("select password from users where username = '%s'", strtoupper(trim($username)));
		$this->connect();
		$mysql_result = mysql_query($sql, $this->db);
		if ($mysql_result == FALSE) {
		} else {
			$arr = mysql_fetch_array($mysql_result);
			$rslt = $arr['password'];
		}
		$this->disconnect();
		return $rslt;
	}
	
	function setPassword($username, $hashedPassword) {
		$rslt = -1;
		# TODO - Do we need to lock the table?
		$sql = sprintf("update users set password = '%s' where username = '%s'", 
			$hashedPassword, strtoupper(trim($username)));
		$this->connect();
		$mysql_result = mysql_query($sql, $this->db);
		if ($mysql_result == FALSE) {
			# TBD
		} else {
			$rslt = 0;
		}
		$this->disconnect();
		return $rslt;
	}
	
	
	function addUser($username, $hashedPassword) {
		$this->connect();
		
		mysql_query(sprintf("set @username = '%s'", $username), $this->db);
		mysql_query(sprintf("set @hashedPassword = '%s'", $hashedPassword), $this->db);
		mysql_query("set @o_status = ''", $this->db);
		mysql_query("CALL addUser(@username, @hashedPassword, @o_status)", $this->db);
		$res = mysql_query("SELECT @o_status as _p_out", $this->db);
		$row = mysql_fetch_array($res);
		$rslt = $row['_p_out'];

		$this->disconnect();
		
		return $rslt;
	}
	
	
	function deactivateUser($username, $hashedPassword) {
		
	}
	
	function getSpaceAvailable($usernme) {
		
		$this->connect();
		$sql = 'select * from parameters';
		$mysql_result = mysql_query($sql, $this->db);
		if ($mysql_result == FALSE) {
		} else {
			$arr = mysql_fetch_array($mysql_result);
			$rslt = intval($arr['default_space_mb']);
		}
		$this->disconnect();
		
		return $rslt;
	}
		
	function addEntity($entity, $token) {
		
	}
	
	
	function getEntityKeys($token) {
		
	}
	
	
	function getEntityKeysByDate($token) {
		
	}
	
	
	function getEntityKeysByType($token) {
		
	}
	
	
	function getAvalailableSharedEntityKeys($token) {
		
	}
	
	
	function shareEntity($entity, $userid, $token) {
		
	}
	
	
	function unshareEntity($entity, $userid, $token) {
		
	}
	
	
	function deleteEntity($entity, $token) {
		
	}
	
	
	
	function startTransaction() {
		
	}
	
	
	function commitTransaction() {
		
	}
	
	
	function rollbackTransaction() {
		
	}
	
	private function connect() {
	  $this->db = mysql_connect("localhost:3306", "root", "P@ssw0rd");
	  mysql_select_db("jot", $this->db);
	}
	
	private function disconnect() {
	  mysql_close($this->db);
	  $this->db = null;		
	}
	
}
?>