<?php
require_once 'IDatabase.php';

class MySqlDatabase {
	
	private $db = FALSE;
	
	public function __construct() {
		$this->connect();
	}
	
	public function getUsernameForToken($token) {
		$rslt = '';
		
		$params = array(':token' => $token);
		$sth = $this->db->prepare('select username from users where is_active = 1 and token = :token');
		$sth->execute($params);
		$arr = $sth->fetch(PDO::FETCH_ASSOC);
		$rslt = $arr['username'];
		$sth->closeCursor();

		return $rslt;
	}
	
	public function getPassword($username) {
		$rslt = '';
		
		$params = array(':username' => $username);
		$sth = $this->db->prepare('select pw from users where username = :username');
		$sth->execute($params);
		$arr = $sth->fetch(PDO::FETCH_ASSOC);
		$rslt = $arr['pw'];
		$sth->closeCursor();

		return $rslt;
	}
	
	public function setPassword($username, $hashedPassword) {
		$rslt = -1;
		
		$params = array(':username' => $username, ':pw' => $hashedPassword);
		$sth = $this->db->prepare('update users set pw = :pw where username = :username');
		$sth->execute($params);
		$rslt = 0;
		$sth->closeCursor();

		return $rslt;
	}
	
	
	public function addUser($username, $hashedPassword) {
		$retval = -1;
		
		$token = "";
		$params = array(':username' => $username, ':pw' => $hashedPassword);
		$sth = $this->db->prepare("CALL addUser(:username, :pw, @o_status)");
		$sth->execute($params);
		$arr = $this->db->query("select @o_status as _p_out")->fetch(PDO::FETCH_ASSOC); 
		$retval = intval($arr['_p_out']);
		$sth->closeCursor();
		
		return $retval;
	}
	
	
	public function deactivateUser($username, $token) {
		$rslt = -1;
		
		$params = array(':username' => $username, ':token' => $token);
		$sth = $this->db->prepare('update users set is_active=0 where username = :username and token = :token');
		$sth->execute($params);
		$rslt = 0;
		$sth->closeCursor();
				
		return $rslt;
	}
	
	public function getSpaceAvailable($username) {
		$rslt = 0;	
		
		$params = array(':username' => $username);
		$sth = $this->db->prepare('select space_remaining_kb from users where is_active=1 and username = :username');
		$sth->execute($params);
		$arr = $sth->fetch(PDO::FETCH_ASSOC);
		$rslt = intval($arr['space_remaining_kb']);
		$sth->closeCursor();
		
		return $rslt;
	}

	public function isActive($username) {
		$rslt = false;	
		
		$params = array(':username' => $username);
		$sth = $this->db->prepare('select is_active from users where username = :username');
		$sth->execute($params);
		$arr = $sth->fetch(PDO::FETCH_ASSOC);
		$rslt = (intval($arr['is_active']) == 1) ? true : false;
		$sth->closeCursor();
		
		return $rslt;
	}
	
	public function setActive($username, $isActive) {
		$rslt = -1;
		
		$params = array(':username' => $username, ':isactive' => $isActive);
		$sth = $this->db->prepare('update users set is_active = :isactive where username = :username');
		$sth->execute($params);
		$rslt = 0;
		$sth->closeCursor();
		
		return $rslt;		
	}
	
	public function createTokenForUser($username) {
		$rslt = "";	
		$token = "";
		$params = array(':username' => $username);
		$sth = $this->db->prepare("CALL createTokenForUser(:username, @token)");
		$sth->execute($params);
		$arr = $this->db->query("select @token as _p_out")->fetch(PDO::FETCH_ASSOC); 
		$token = $arr['_p_out'];
		$rslt = $token;			
		$sth->closeCursor();
		
		return $rslt;
	}
	
	# Adds or updates the given entity.
	# Returns the entity id.
	public function setEntity($token, $entity) {
		$rslt = -1;	
		$title = $entity->getKey();
		$params = array(':token' => $token, ':title' => $title);
		$sth = $this->db->prepare("CALL storeEntity(:token, :title, @identity)");
		$sth->execute($params);
		$arr = $this->db->query("select @identity as _p_out")->fetch(PDO::FETCH_ASSOC); 
		$identity= intval($arr['_p_out']);
		$rslt = $identity;
		
		return $rslt;
	}
	
	function setEntityItem($token, $entityItem, $entityId) {
		$rslt = -1;
		
		# not implemented
		$valArray = $entityItem->getData();
	    $itemid = $valArray['itemid'];
	    $itemtype = $valArray['itemtype'];
	    $annotation = $valArray['annotation'];
	    $bdata = $valArray['bdata'];
	    # bdata is base64-encoded, so decode it.
	    $binary = "";
	    if (strlen($bdata) > 0) {
	    	$binary = base64_decode($bdata);
	    }
	    
		$params = array(
			':token' => $token,
			':entityid' => $entityId, 
			':itemid' => $itemid,
			':itemtype' => $itemtype,
			':annotation' => $annotation,
			':bdata' => $binary
		);
		$sth = $this->db->prepare("CALL storeEntityItem(:token, :entityid, :itemid,
							:itemtype, :annotation, :bdata, @returnValue)");
		$sth->execute($params);
		$arr = $this->db->query("select @returnValue as _p_out")->fetch(PDO::FETCH_ASSOC); 
		$rslt = intval($arr['_p_out']);

		return $rslt;
	}
	
	function getEntity($token, $key) {
		$rslt = new Entity();
		$rslt->setKey($key);
		
		$params = array(':token' => $token, ':key' => $key);
		# find entities that has user that has token
		# and that has items that has type = input type.
		$sth = $this->db->prepare(
			'select ei.identity_items, it.friendly_name, ei.annotation, ei.item ' .
			'from entity_items ei ' .
			'inner join entities e on e.identities = ei.identities ' .
			'inner join item_types it on it.iditem_types = ei.iditem_types ' .
			'inner join users u on u.iduser = e.iduser ' .
			'where u.token = :token and e.title = :key');
		$sth->execute($params);
		while ($arr = $sth->fetch(PDO::FETCH_ASSOC)) {
			$item = new EntityItem();
			$item->setItemId($arr['identity_items']);
			$itemType = $arr['friendly_name'];
			$item->setItemType($itemType);
			$item->setAnnotation($arr['annotation']);
			if ($itemType != 'text') {
				$blob = $arr['item'];
				$item->setBdata(base64_encode($blob));
				#$item->setBdata(base64_encode("TBD...there is a bug with values over 4575 in length."));
			}
			$rslt->addItem($item);
		}
		$sth->closeCursor();
		
		return $rslt;
	}
	
	public function getAllKeys($token) {
		$rslt = array();
		
		$params = array(':token' => $token);
		# find entities that has user that has token
		# and that has items that has type = input type.
		$sth = $this->db->prepare(
			"select e.title, e.last_modified, ( " .
			    "select sum(if(it.friendly_name='text', length(ei.annotation), length(ei.item))) as items_size " .
			    "from entity_items ei " .
			    "inner join item_types it on it.iditem_types=ei.iditem_types where ei.identities=e.identities) as items_size " .
			"from entities e " .
			"inner join users u on u.iduser=e.iduser " .
			"where u.token = :token"); 
		$sth->execute($params);
		$idx = 0;
		while ($arr = $sth->fetch(PDO::FETCH_ASSOC)) {
			$entity = new Entity();
			$entity->setKey($arr['title']);
			$entity->setModified($arr['last_modified']);
			$entity->setItemsSize($arr['items_size']);
			$rslt[$idx] = $entity;
			$idx++;
		}
		$sth->closeCursor();

		return $rslt;
	}
	
	public function getEntityKeysByDate($token, $date) {
		
	}
	
	# Return a string array
	public function getEntityKeysByType($token, $type) {
		$rslt = array();
		
		$params = array(':token' => $token, ':item_type' => $type);
		# find entities that has user that has token
		# and that has items that has type = input type.
		$sth = $this->db->prepare(
			'select distinct e.title from entities e '.
			'inner join users u on u.iduser=e.iduser '.
			'inner join entity_items ei on ei.identities = e.identities '.
			'inner join item_types it on it.iditem_types = ei.iditem_types '.
			'where u.token = :token and it.friendly_name = lower(:item_type)');
		$sth->execute($params);
		$idx = 0;
		while ($arr = $sth->fetch(PDO::FETCH_ASSOC)) {
			$rslt[$idx] = $arr['title'];
			$idx++;
		}
		$sth->closeCursor();

		return $rslt;
	}
	
	public function getAvalailableSharedEntityKeys($token) {
		
	}
	
	public function shareEntity($entity, $userid, $token) {
		
	}
	
	public function unshareEntity($entity, $userid, $token) {
		
	}
	
	public function deleteEntity($entity, $token) {
		
	}
	
	public function startTransaction() {
		$this->db->query("SET autocommit=0;");
		$this->db->query("start transaction;");
	}
	
	public function commit() {
		$this->db->query("commit;");
		$this->db->query("SET autocommit=1;");
	}
	
	public function rollback() {
		$this->db->query("rollback;");
		$this->db->query("SET autocommit=1;");
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
