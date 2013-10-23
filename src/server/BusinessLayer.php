<?php
include_once 'Security.php';
include_once 'IDatabase.php';
include_once 'DBFactory.php';

class BusinessLayer {
	private $db = null;
		
	function BusinessLayer() {
		$this->db = DBFactory::getDatabase();
	}
	
	function isUserValid($username, $password) {
		$rslt = false;
		# retrieve hashed password from DB for this user
		# validate $hashedPassword with arg
		if ($this->db->isActive($username) == true) {
			$hashedPassword = $this->db->getPassword($username);
			if (strlen($hashedPassword) > 0) {
				$rslt = Security::isValid($password, $hashedPassword);
			}	
		}
		return $rslt;
	}
	
	function setNewPassword($username, $oldPassword, $newPassword) {
		if ($this->isUserValid($username, $oldPassword)) {
			$hashedPassword = Security::encryptPassword($newPassword);
			$rslt = $this->db->setPassword($username, $hashedPassword);
		}
	}
	
	function addUser($username, $password) {
		$token = -7;
		$hashedPassword = $this->db->getPassword($username);
		if (strlen($hashedPassword) == 0) {
			# no user, so add this one
			$hashedPassword = Security::encryptPassword($password);
			$token = $this->db->addUser($username, $hashedPassword);
		}
		return $token;
	}
	
	function deactivateUser($username, $password) {
		if ($this->isUserValid($username, $password)) {
			$this->db->deactivateUser($username);
		}
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
}
?>