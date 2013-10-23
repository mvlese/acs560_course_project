<?php
include_once 'IDatabase.php';

class MySqlDatabase implements IDatabase {
	
	private $db = FALSE;
	
	function MysSqlDatabase() {
	}
	
	function getPassword($username) {
		$rslt = '';
		$sql = sprintf("select pw from users where is_active=1 and username = '%s'", $this->getUsername($username));
		$this->connect();
		$mysql_result = mysql_query($sql, $this->db);
		if ($mysql_result == FALSE) {
		} else {
			$arr = mysql_fetch_array($mysql_result);
			$rslt = $arr['pw'];
		}
		$this->disconnect();
		return $rslt;
	}
	
	function setPassword($username, $hashedPassword) {
		$rslt = -1;
		# TODO - Do we need to lock the table?
		$sql = sprintf("update users set pw = '%s' where username = '%s'", 
			$hashedPassword, $this->getUsername($username));
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
		
		$rslt = mysql_query(sprintf("set @username = '%s'", $this->getUsername($username)), $this->db);
		$rslt = mysql_query(sprintf("set @hashedPassword = '%s'", $hashedPassword), $this->db);
		$rslt = mysql_query("set @o_status = ''", $this->db);
		$rslt = mysql_query("CALL addUser(@username, @hashedPassword, @o_status)", $this->db);
		$res = mysql_query("SELECT @o_status as _p_out", $this->db);
		$row = mysql_fetch_array($res);
		$rslt = $row['_p_out'];

		$this->disconnect();
		
		return $rslt;
	}
	
	
	function deactivateUser($username) {
		$this->connect();
		$rslt = -1;
		$sql = sprintf("update users set is_active=0 where username = '%s'", $this->getUsername($username));
		$mysql_result = mysql_query($sql, $this->db);
		if ($mysql_result != FALSE) {
			$rslt = 0;
		}
		$this->disconnect();
		
		return $rslt;
	}
	
	function getSpaceAvailable($username) {
		$rslt = 0;	
		$this->connect();
		
		$sql = sprintf("select space_remaining_kb from users where is_active=1 and username = '%s'", $this->getUsername($username));
		$mysql_result = mysql_query($sql, $this->db);
		if ($mysql_result == FALSE) {
		} else {
			$arr = mysql_fetch_array($mysql_result);
			$rslt = intval($arr['space_remaining_kb']);
		}
		
		$this->disconnect();
		
		return $rslt;
	}

	function isActive($username) {
		$rslt = false;	
		$this->connect();
		
		$sql = sprintf("select is_active from users where username = '%s'", $this->getUsername($username));
		$mysql_result = mysql_query($sql, $this->db);
		if ($mysql_result == FALSE) {
		} else {
			$arr = mysql_fetch_array($mysql_result);
			$rslt = (intval($arr['is_active']) == 1) ? true : false;
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
		$link = mysql_connect("localhost:3306", "acs560", "acs560@se");
		if (!$link) {
		} else {
			$this->db = $link;
	  		mysql_select_db("jot", $this->db);
		}
	}
	
	private function disconnect() {
	  mysql_close($this->db);
	  $this->db = null;		
	}
	
	private function getUsername($username) { 
		return strtoupper(trim($username));
	}
}
?>