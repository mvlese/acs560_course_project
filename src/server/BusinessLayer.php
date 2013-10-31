<?php
include_once 'Security.php';
include_once 'IDatabase.php';
include_once 'DBFactory.php';

class BusinessLayer {
	private $db = null;
		
	function __construct() {
		$this->db = DBFactory::getDatabase();
	}
	
	function logon($username, $password) {
		$rslt = '';
		# Do not check isActive flag.  If the user attempts
		# to logon, he is active.
		if ($this->isUserValid($username, $password, true)) {
			$iRslt = $this->db->setActive($username, 1);
			if ($iRslt == 0) {
				$token = $this->db->createTokenForUser($username);
				$rslt = $token;
			}
		}
		return $rslt;
	}
	
	function setNewPassword($username, $oldPassword, $newPassword) {
		$rslt = -1;
		
		if ($this->isUserValid($username, $oldPassword)) {
			$hashedPassword = Security::encryptPassword($newPassword);
			$rslt = $this->db->setPassword($username, $hashedPassword);
		}
		
		return $rslt;
	}
	
	function registerNewUser($username, $password) {
		$rslt = '';
		$hashedPassword = $this->db->getPassword($username);
		if (strlen($hashedPassword) == 0) {
			# no user, so add this one
			$hashedPassword = Security::encryptPassword($password);
			$rslt = $this->db->addUser($username, $hashedPassword);
			if ($rslt == 0) {
				$token = $this->db->createTokenForUser($username);
				$rslt = $token;
			}
		}
		
		return $rslt;
	}
	
	function deactivateUser($token, $username, $password) {
		$rslt = -1;
		if ($this->isUserValid($username, $password)) {
			$rslt = $this->db->deactivateUser($username, $token);
		}
		
		return $rslt;
	}
		
	function getSpaceRemainingKbForUser($username, $password) {
		$rslt = 0;
		if ($this->db->isActive($username) == true) {
			if ($this->isUserValid($username, $password)) {
				$rslt = $this->db->getSpaceAvailable($username);
			}
		}
		
		return $rslt;
	}

	private function isUserValid($username, $password, $doNotCheckIsActive = false) {
		$rslt = false;
		# retrieve hashed password from DB for this user
		# validate $hashedPassword with arg
		if ($doNotCheckIsActive || ($this->db->isActive($username) == true)) {
			$hashedPassword = $this->db->getPassword($username);
			if (strlen($hashedPassword) > 0) {
				$rslt = Security::isValid($password, $hashedPassword);
			}	
		}
		return $rslt;
	}
	
}
?>