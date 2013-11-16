<?php
interface IDatabase {
	function getPassword($username);
	function setPassword($username, $hashedPassword);
	function addUser($username, $hashedPassword);
	function deactivateUser($username, $token);
	function getSpaceAvailable($username);
	function createTokenForUser($username);
	function isActive($username);
	function setActive($username, $isActive);
	
	# Adds or updates the given entity.
	# Returns the entity id.
	function setEntity($token, $entity);
	
	# Adds or updates the given entity item.
	function setEntityItem($token, $entityItem, $entityId);
	
	function getAllKeys($token);
	function getEntityKeysByDate($token, $startDate, $endDate);
	function getEntityKeysByType($token, $type);
	function getAvalailableSharedEntityKeys($token);
	function shareEntity($entity, $userid, $token);
	function unshareEntity($entity, $userid, $token);
	function deleteEntity($key, $token);
	function deleteEntityItem($key, $itemid, $token);
	
	function startTransaction();
	function commit();
	function rollback();
}
?>
