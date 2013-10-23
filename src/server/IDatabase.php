<?php
interface IDatabase {
	function getPassword($username);
	function setPassword($username, $hashedPassword);
	function addUser($username, $hashedPassword);
	function deactivateUser($username);
	function getSpaceAvailable($username);
	function isActive($username);
	
	function addEntity($entity, $token);
	function getEntityKeys($token);
	function getEntityKeysByDate($token);
	function getEntityKeysByType($token);
	function getAvalailableSharedEntityKeys($token);
	function shareEntity($entity, $userid, $token);
	function unshareEntity($entity, $userid, $token);
	function deleteEntity($entity, $token);
	
	function startTransaction();
	function commitTransaction();
	function rollbackTransaction();
}
?>
