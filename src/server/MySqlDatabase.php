<?php
require_once 'IDatabase.php';

class MySqlDatabase implements IDatabase {
	
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
	
	public function setEntityItem($token, $entityItem, $entityId) {
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
	
	public function getEntity($token, $key) {
		$isShared = $this->isShared($token, $key);
		if ($isShared) {
			$rslt = $this->getSharedEntity($token, $key);
		} else {
			$rslt = $this->getPrivateEntity($token, $key);
		}
		
		return $rslt;
	}
	
	# Return a SearchItem array
	public function getAllKeys($token) {
		$rslt = array();
		
		$params = array(':token' => $token);
		# find entities that has user that has token
		# and that has items that has type = input type.
		$sth = $this->db->prepare(
			"select e.title, e.last_modified, ( " .
			    "select sum(getSize(ei.identity_items, ei.identities)) " .
			    "from entity_items ei " .
			    "inner join item_types it on it.iditem_types=ei.iditem_types where ei.identities=e.identities) as items_size " .
			"from entities e " .
			"inner join users u on u.iduser=e.iduser " .
			"where u.token = :token"); 
		$sth->execute($params);
		$idx = 0;
		while ($arr = $sth->fetch(PDO::FETCH_ASSOC)) {
			$searchItem = new SearchItem();
			$searchItem->setKey($arr['title']);
			$searchItem->setModified($arr['last_modified']);
			$searchItem->setItemSize($arr['items_size']);
			$rslt[$idx] = $searchItem;
			$idx++;
		}
		$sth->closeCursor();

		return $rslt;
	}
	
	# $date is DateTime object.
	# Return a SearchItem array
	public function getEntityKeysByDate($token, $startDate, $endDate) {
		$rslt = array();
		
		logger('start date: ' . $startDate->format("Y-m-d") . "\n");
		# Add one day to the end date.
		$endDate = $endDate->add(new DateInterval('P1D'));
		logger('end date: ' . $endDate->format("Y-m-d") . "\n");

		$params = array(
			':token' => $token, 
			':startdate' => $startDate->format("Y-m-d"), 
			':enddate' => $endDate->format("Y-m-d"));
		# find entities that has user that has token
		# and that has items that has type = input type.
		$sth = $this->db->prepare(
			'select distinct e.title, e.last_modified, ( ' .
			    'select sum(getSize(ei.identity_items, ei.identities)) ' .
			    'from entity_items ei ' .
			    'inner join item_types it on it.iditem_types=ei.iditem_types where ei.identities=e.identities) as items_size ' . 
			'from entities e '.
			'inner join users u on u.iduser=e.iduser '.
			'inner join entity_items ei on ei.identities = e.identities '.
			'where u.token = :token and e.last_modified >= :startdate and e.last_modified < :enddate');
		$sth->execute($params);
		$idx = 0;
		while ($arr = $sth->fetch(PDO::FETCH_ASSOC)) {
			$item = new SearchItem();
			$item->setKey($arr['title']);
			$item->setModified($arr['last_modified']);
			$item->setItemSize($arr['items_size']);
			$rslt[$idx] = $item;
			$idx++;
		}
		$sth->closeCursor();

		return $rslt;
	}
	
	# Return a SearchItem array
	public function getEntityKeysByType($token, $type) {
		$rslt = array();
		
		$params = array(':token' => $token, ':item_type' => $type);
		# find entities that has user that has token
		# and that has items that has type = input type.
		$sth = $this->db->prepare(
			'select distinct e.title, e.last_modified, ( ' .
			    'select sum(getSize(ei.identity_items, ei.identities)) ' .
			    'from entity_items ei ' .
			    'inner join item_types it on it.iditem_types=ei.iditem_types where ei.identities=e.identities) as items_size ' . 
			'from entities e '.
			'inner join users u on u.iduser=e.iduser '.
			'inner join entity_items ei on ei.identities = e.identities '.
			'inner join item_types it on it.iditem_types = ei.iditem_types '.
			'where u.token = :token and it.friendly_name = lower(:item_type)');
		$sth->execute($params);
		$idx = 0;
		while ($arr = $sth->fetch(PDO::FETCH_ASSOC)) {
			$item = new SearchItem();
			$item->setKey($arr['title']);
			$item->setModified($arr['last_modified']);
			$item->setItemSize($arr['items_size']);
			$rslt[$idx] = $item;
			$idx++;
		}
		$sth->closeCursor();

		return $rslt;
	}
	
	# Return the keys of those entities shared to the owner of the token.
	public function getAvalailableSharedEntityKeys($token) {
		$rslt = array();
		
		$params = array(':token' => $token);
		$sth = $this->db->prepare(
				'select distinct e.title from entities e ' . 
				'inner join shared_entities se on se.identities = e.identities ' .
				'inner join users u on u.iduser = se.to_userid ' .
				'where u.token = :token');
		$sth->execute($params);
		$idx = 0;
		while ($arr = $sth->fetch(PDO::FETCH_ASSOC)) {
			$rslt[$idx] = $arr['title'];
			$idx++;
		}
		$sth->closeCursor();
		
		return $rslt;
	}
	
	public function shareEntity($token, $entity, $toShareWithUsername) {
		$rslt = -1;

		$key = $entity->getKey();
		foreach ($entity->getItems() as $item) {
			$itemid = $item->getItemId();
			$params = array(
				':token' => $token,
				':key' => $key, 
				':itemid' => intval($itemid),
				':shareWith' => $toShareWithUsername
			);			
			$sth = $this->db->prepare(
					"CALL shareEntity(:token, :key, :itemid, :shareWith)");
			$sth->execute($params);
			$sth->closeCursor();
		}
		$rslt = 0;
		
		return $rslt;
	}
	
	public function unshareEntity($token, $entity, $toUnshareWithUsername) {
		$rslt = -1;
		# not implemented
		return $rslt;
	}
	
	public function deleteEntity($key, $token) {
		$rslt = -1;
		$params = array(':key' => $key, ':token' => $token);
		$sth = $this->db->prepare(
			'delete from entities ' .
			'using entities, users ' .
			'where entities.title = :key and users.token = :token ' .
			'and entities.iduser = users.iduser');
		$sth->execute($params);
		# if here without throwing an exception, should be good.
		$rslt = 0;
		$sth->closeCursor();
		
		return $rslt;
	}
	
	# If there are no items left after deleting this one, then the entity will
	# be deleted as well.
	public function deleteEntityItem($key, $itemid, $token) {
		$rslt = -1;
		$params = array(':key' => $key, ':token' => $token, ':itemid' => $itemid);
		$sth = $this->db->prepare(
			'delete from entity_items ' .
			'using entity_items, entities, users ' .
			'where entities.title = :key ' . 
		        'and users.token = :token ' .
		        'and entities.iduser = users.iduser ' .
		        'and entity_items.identities = entities.identities ' .
		        'and entity_items.identity_items = :itemid');
		$sth->execute($params);
		$sth->closeCursor();
		$rslt = 0;
		
		# Now find out if the item list is empty for this entity.  If so,
		# then delete the entity, too.
		$params = array(':key' => $key, ':token' => $token);
		$sth = $this->db->prepare(
			'select count(*) as cnt from entity_items ' .
			'inner join entities on entities.identities = entity_items.identities ' .
			'inner join users on users.iduser = entities.iduser ' .
			'where entities.title = :key and users.token = :token');
		$sth->execute($params);
		$arr = $sth->fetch(PDO::FETCH_ASSOC);
		$itemCount = intval($arr['cnt']);
		if ($itemCount == 0) {
			logger(sprintf("no more items, deleting entity: %s\n", $key));
			$rslt = $this->deleteEntity($key, $token);
		}
		$sth->closeCursor();
		
		return $rslt;
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

	# This function returns true if the $key is a shared entity the user
	# with the given token can view.
	private function isShared($token, $key) {
		$params = array(':key' => $key, ':token' => $token);
		$sth = $this->db->prepare(
			'select count(*) as cnt from shared_entities se ' .
			'inner join users u on u.iduser=se.to_userid ' .
			'inner join entities e on e.identities = se.identities ' .
			'where u.token = :token and e.title = :key');
		$sth->execute($params);
		$arr = $sth->fetch(PDO::FETCH_ASSOC);
		$itemCount = intval($arr['cnt']);
		
		$rslt = ($itemCount > 0);
		
		return $rslt;
	}
	
	private function createEntityItemFromArray($array) {
		$item = new EntityItem();
		$item->setItemId($array['identity_items']);
		$item->setItemType($array['friendly_name']);
		$item->setAnnotation($array['annotation']);
		$raw = $array['item'];
		$encoded = base64_encode($raw);
		$item->setBdata($encoded);
			
		return $item;		
	}
	
	private function getPrivateEntity($token, $key) {
		$rslt = new Entity();
		$rslt->setKey($key);
		
		$params = array(':token' => $token, ':key' => $key);
		# find items that has user that has token
		# and that has items that has type = input type.
		$sth = $this->db->prepare(
			'select ei.identity_items, it.friendly_name, ei.annotation, ei.item ' .
			'from entity_items ei ' .
			'inner join entities e on e.identities = ei.identities ' .
			'inner join item_types it on it.iditem_types = ei.iditem_types ' .
			'inner join users u on u.iduser = e.iduser ' .
			'where u.token = :token and e.title = :key ' .
			'order by ei.identity_items');
		$sth->execute($params);
		while ($arr = $sth->fetch(PDO::FETCH_ASSOC)) {
			$item = $this->createEntityItemFromArray($arr);
			$rslt->addItem($item);
		}
		$sth->closeCursor();
		
		return $rslt;
	}
	
	private function getSharedEntity($token, $key) {
		$rslt = new Entity();
		$rslt->setKey($key);

		$params = array(':token' => $token, ':key' => $key);
		# Find all items in shared_entities that are items of the entity with
		# key that can be viewed by the user that has token. 
		$sth = $this->db->prepare(
			'select ei.identity_items, it.friendly_name, ei.annotation, ei.item from shared_entities se ' .
			'inner join users u on u.iduser=se.to_userid ' .
			'inner join entity_items ei on ei.identity_items = se.identity_items ' .
			'inner join entities e on (e.identities = se.identities and ei.identities = e.identities) ' .
			'inner join item_types it on it.iditem_types=ei.iditem_types ' .
			'where u.token = :token and e.title = :key ' . 
			'order by ei.identity_items');
		$sth->execute($params);
		while ($arr = $sth->fetch(PDO::FETCH_ASSOC)) {
			$item = $this->createEntityItemFromArray($arr);
			$rslt->addItem($item);
		}
		$sth->closeCursor();
		
		return $rslt;
	}
	
	
	private function connect() {
		$dsn = 'mysql:dbname=lese_jot;host=localhost';
		$user = 'acs560';
		$password = 'jotitdown';
		if ($this->db == FALSE) {
			try {
				$this->db = new PDO($dsn, $user, $password);
			} catch (PDOException $e) {
			}
		}
	}
/*	
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
*/	
}
?>
