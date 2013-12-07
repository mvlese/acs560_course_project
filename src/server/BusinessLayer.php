<?php
require_once 'Security.php';
require_once 'IDatabase.php';
require_once 'DBFactory.php';
require_once 'EntityItem.php';
require_once 'Entity.php';
require_once 'EntityResult.php';
require_once 'SearchKeyResult.php';

class BusinessLayer {
	# We use transactions for all database operations.
	private $db = null;
		
	function __construct() {
		$this->db = DBFactory::getDatabase();
	}
	
	public function logon($username, $password) {
		$rslt = '';
		try {
			$this->db->startTransaction();
			# Do not check isActive flag.  If the user attempts
			# to logon, he is active.
			if ($this->isUserValid($username, $password, true)) {
				$iRslt = $this->db->setActive($username, 1);
				if ($iRslt == 0) {
					$token = $this->db->createTokenForUser($username);
					$rslt = $token;
				}
			}
			$this->db->commit();
		}
		catch(Exception $ex) {
			$this->db->rollback();
			$rslt = '';
		}
		
		return $rslt;
	}
	
	public function setNewPassword($username, $oldPassword, $newPassword) {
		$rslt = -1;
		
		try{
			$this->db->startTransaction();
			if ($this->isUserValid($username, $oldPassword)) {
				$hashedPassword = Security::encryptPassword($newPassword);
				$rslt = $this->db->setPassword($username, $hashedPassword);
			}
			$this->db->commit();
		}
		catch(Exception $ex) {
			$this->db->rollback();
			$rslt = -1;
		}
		
		return $rslt;
	}
	
	public function registerNewUser($username, $password) {
		$rslt = '';
		try{
			$this->db->startTransaction();
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
			$this->db->commit();
		}
		catch(Exception $ex) {
			$this->db->rollback();
			$rslt = -1;
		}
		
		return $rslt;
	}
	
	public function changePassword($token, $newPassword)
	{
		$retval = -1;
		try{
			$this->db->startTransaction();
			$username = $this->db->getUsernameForToken($token); 
			if (strlen($username) > 0) {
				$hashedPassword = Security::encryptPassword($newPassword);
				$this->db->setPassword($username, $hashedPassword);
				$retval = 0;
			}	
			$this->db->commit();
		}
		catch(Exception $ex) {
			$this->db->rollback();
			$rslt = -1;
		}
		
		return $retval;
	}
	
	public function deactivateUser($token, $username, $password) {
		$rslt = -1;
		try{
			$this->db->startTransaction();
			if ($this->isUserValid($username, $password)) {
				$rslt = $this->db->deactivateUser($username, $token);
			}
			$this->db->commit();
		}
		catch(Exception $ex) {
			$this->db->rollback();
			$rslt = -1;
		}
		
		return $rslt;
	}
		
	public function getSpaceRemainingKbForUser($username, $password) {
		$rslt = 0;
		try{
			$this->db->startTransaction();
			if ($this->db->isActive($username) == true) {
				if ($this->isUserValid($username, $password)) {
					$rslt = $this->db->getSpaceAvailable($username);
				}
			}
			$this->db->commit();
		}
		catch(Exception $ex) {
			$this->db->rollback();
			$rslt = -1;
		}
		
		return $rslt;
	}

	public function storeEntity($token, $entity) {
		$rslt = -1;
		try{
			$this->db->startTransaction();
			$username = $this->db->getUsernameForToken($token); 
			if (strlen($username) > 0) {
				$title = $entity->getKey();
				// Valid token
				// Store /update entity.
				$entityId = $this->db->setEntity($token, $entity);
				if ($entityId > 0) {
					$rslt = 0;
					$items = $entity->getItems();
					// Entity is an array of EntityItems.
					foreach($items as $entityItem) {
						$rslt = $this->db->setEntityItem($token, $entityItem, $entityId);
						if ($rslt != 0) {
							break;
						}
					}
				}
			}
			if ($rslt == 0) {
				# Commit on success.
				$this->db->commit();
			} else {
				$this->db->rollback();
			}
		}
		catch(Exception $ex) {
			$this->db->rollback();
			$rslt = -1;
		}
		
		return $rslt;
	}	
	
	public function getByDate($token, $startDate, $endDate) {
		$rslt = new SearchKeyResult();
		$rslt->setResult(-1);
		try{
			$arr = $this->isDateRangeValid($startDate, $endDate);
			if (count($arr) == 2) {
				$this->db->startTransaction();
				$arr = $this->db->getEntityKeysByDate($token, $arr[0], $arr[1]);
				$this->db->commit();
				$rslt->setResult(0);
				foreach($arr as $value) {
					$rslt->addItem($value);
				}
			}
		}
		catch(Exception $ex) {
			$this->db->rollback();
			$rslt = new SearchKeyResult();
			$rslt->setResult(-1);
		}

		return $rslt;		
	}

	public function getByType($token, $type) {
		$rslt = new SearchKeyResult();
		$rslt->setResult(-1);
		try{
			$this->db->startTransaction();
			$arr = $this->db->getEntityKeysByType($token, $type);
			$this->db->commit();
			$rslt->setResult(0);
			foreach($arr as $value) {
				$item = new SearchItem();
				$item->setKey($value);
				$rslt->addItem($item);
			}
		}
		catch(Exception $ex) {
			$this->db->rollback();
			$rslt = new SearchKeyResult();
			$rslt->setResult(-1);
		}

		return $rslt;		
	}

	public function getEntity($token, $key) {
		$rslt = new EntityResult();
		$rslt->setResult(-1);
		try{
			$this->db->startTransaction();
			$entity = $this->db->getEntity($token, $key);
			$rslt->addItem($entity);
			$rslt->setResult(0);
			
			$this->db->commit();
		}
		catch(Exception $ex) {
			$this->db->rollback();
			$rslt = new EntityResult();
			$rslt->setResult(-1);
		}
		
		return $rslt;
	}

	public function getAllKeys($token) {
		$rslt = new SearchKeyResult();

		$rslt->setResult(-1);
		try{
			$this->db->startTransaction();
			$arr = $this->db->getAllKeys($token);
			$this->db->commit();
			$rslt->setResult(0);
			foreach($arr as $value) {
				$rslt->addItem($value);
			}
		}
		catch(Exception $ex) {
			$this->db->rollback();
			$rslt = new SearchKeyResult();
			$rslt->setResult(-1);
		}
			
		return $rslt;
	}
	
	# This function deletes the items that are in this entity.  Some or all
	# of the original items(only itemid need to be populated) may be in the
	# entity.  If no items are listed, then the entire entity will be deleted.
	public function deleteEntity($token, $entity) {
		$rslt = -1;
		try{
			$this->db->startTransaction();
			$username = $this->db->getUsernameForToken($token); 
			if (strlen($username) > 0) {
				$key = $entity->getKey();
				$items = $entity->getItems();
				if (count($items) == 0) {
					# delete entire entity
					$rslt = $this->db->deleteEntity($key, $token);
				} else {
					# Delete only the items in this entity.  If there are no
					# items left after deleting these, then the entity will
					# be deleted as well.
					foreach($items as $item) {
						$rslt = $this->db->deleteEntityItem(
									$key, $item->getItemId(), $token);
					}
				}
			}
			if ($rslt == 0) {
				# Commit on success.
				$this->db->commit();
			} else {
				$this->db->rollback();
			}
		}
		catch(Exception $ex) {
			logger("in deleteEntityItem exception\n");
			$this->db->rollback();
			$rslt = -1;
		}
		
		return $rslt;
	}
	
	public function shareEntity($token, $entity, $toShareWithUsername) {
		$rslt = -1;
		try{
			$this->db->startTransaction();
			$username = $this->db->getUsernameForToken($token); 
			if (strlen($username) > 0) {
				$rslt = $this->db->shareEntity(
						$token, $entity, $toShareWithUsername);
			}
			$this->db->commit();
		}
		catch(Exception $ex) {
			logger("in shareEntity exception\n");
			$this->db->rollback();
			$rslt = -1;
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
	
	private function isDateRangeValid($startDate, $endDate) {
		$isValid = true;
		$d1 = new DateTime();
		$d2 = new DateTime();
		$rslt = array();
		
		$arr = date_parse($startDate);
		$isValid &= (count($arr['errors']) == 0);
		if ($isValid)  {
			$d1->setDate($arr['year'], $arr['month'], $arr['day']);
			$arr = date_parse($endDate);
			$isValid &= (count($arr['errors']) == 0);
			if ($isValid)  {
				$d2->setDate($arr['year'], $arr['month'], $arr['day']);
				$isValid &= ($d2->getTimestamp() >= $d1->getTimestamp());
				if ($isValid) {
					$rslt = array($d1, $d2);
				}
			}
		}		
		
		return $rslt;
	}
	
}
?>
