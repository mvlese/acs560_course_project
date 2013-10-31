<?php
include_once 'IDatabase.php';

class MySqlDatabase implements IDatabase {
	
	private $db = FALSE;
	
	function __construct() {
		$this->connect();
	}
	
	function getPassword($username) {
		$rslt = '';
		
		$params = array(':username' => $username);
		try{
			$sth = $this->db->prepare('select pw from users where username = :username');
			$sth->execute($params);
			$arr = $sth->fetch(PDO::FETCH_ASSOC);
			$rslt = $arr['pw'];
			$sth->closeCursor();
		} catch(PDOException $e) {
			$rslt = '';
		}

		return $rslt;
	}
	
	function setPassword($username, $hashedPassword) {
		$rslt = -1;
		# TODO - Do we need to lock the table?
		
		$params = array(':username' => $username, ':pw' => $hashedPassword);
		try{
			$sth = $this->db->prepare('update users set pw = :pw where username = :username');
			$sth->execute($params);
			$rslt = 0;
			$sth->closeCursor();
		} catch(PDOException $e) {
			$rslt = -1;
		}

		return $rslt;
	}
	
	
	function addUser($username, $hashedPassword) {
		$retval = -1;
		
		try{
			$token = "";
			$params = array(':username' => $username, ':pw' => $hashedPassword);
			$sth = $this->db->prepare("CALL addUser(:username, :pw, @o_status)");
			$sth->execute($params);
			$arr = $this->db->query("select @o_status as _p_out")->fetch(PDO::FETCH_ASSOC); 
			$retval = intval($arr['_p_out']);
			$sth->closeCursor();
		} catch(PDOException $e) {
			$retval = -1;
		}
		
		return $retval;
	}
	
	
	function deactivateUser($username, $token) {
		$rslt = -1;
		
		$params = array(':username' => $username, ':token' => $token);
		try{
			$sth = $this->db->prepare('update users set is_active=0 where username = :username and token = :token');
			$sth->execute($params);
			$rslt = 0;
			$sth->closeCursor();
		} catch(PDOException $e) {
			$rslt = -1;
		}
				
		return $rslt;
	}
	
	function getSpaceAvailable($username) {
		$rslt = 0;	
		
		$params = array(':username' => $username);
		try{
			$sth = $this->db->prepare('select space_remaining_kb from users where is_active=1 and username = :username');
			$sth->execute($params);
			$arr = $sth->fetch(PDO::FETCH_ASSOC);
			$rslt = intval($arr['space_remaining_kb']);
			$sth->closeCursor();
		} catch(PDOException $e) {
			$rslt = 0;
		}
		
		return $rslt;
	}

	function isActive($username) {
		$rslt = false;	
		
		$params = array(':username' => $username);
		try{
			$sth = $this->db->prepare('select is_active from users where username = :username');
			$sth->execute($params);
			$arr = $sth->fetch(PDO::FETCH_ASSOC);
			$rslt = (intval($arr['is_active']) == 1) ? true : false;
			$sth->closeCursor();
		} catch(PDOException $e) {
			$rslt = false;
		}
		
		return $rslt;
	}
	
	function setActive($username, $isActive) {
		$rslt = -1;
		
		$params = array(':username' => $username, ':isactive' => $isActive);
		try{
			$sth = $this->db->prepare('update users set is_active = :isactive where username = :username');
			$sth->execute($params);
			$rslt = 0;
			$sth->closeCursor();
		} catch(PDOException $e) {
			$rslt = -1;
		}
		
		return $rslt;		
	}
	
	function createTokenForUser($username) {
		$rslt = "";	
		try{
			$token = "";
			$params = array(':username' => $username);
			$sth = $this->db->prepare("CALL createTokenForUser(:username, @token)");
			$sth->execute($params);
			$arr = $this->db->query("select @token as _p_out")->fetch(PDO::FETCH_ASSOC); 
			$token = $arr['_p_out'];
			$rslt = $token;			
			$sth->closeCursor();
		} catch(PDOException $e) {
			$rslt = "";
		}
		
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
		$dsn = 'mysql:dbname=jot;host=localhost';
		$user = 'acs560';
		$password = 'acs560@se';
		if ($this->db == FALSE) {
			try {
				$this->db = new PDO($dsn, $user, $password);
			} catch (PDOException $e) {
			}
		}
	}
	
}
?>