<?php
require_once 'methods2.php';

header('Access-Control-Allow-Origin: *');

function getSearchKeyResultToJsonCompatible($searchKeyResult) {
	$rslt = new stdClass();
	
	$rslt->result = $searchKeyResult['result'];
	$rslt->errorMessage = $searchKeyResult['errorMessage'];
	$rslt->searchKeyItems = array();
	
	$idx = 0;
    foreach($searchKeyResult['searchKeyItems'] as $value) {
    	$item = new stdClass();
    	$item->key = $value['key'];
    	$item->owner = $value['owner'];
    	$item->item_memory_bytes = $value['item_memory_bytes'];
    	$item->modified = $value['modified'];
    	$rslt->items[$idx++] = $item;
    }
    
    return $rslt;
}

function getEntityResultToJsonCompatible($entityResult) {
	$rslt = new stdClass();
	
	$rslt->result = $entityResult['result'];
	$rslt->errorMessage = $entityResult['errorMessage'];
	$rslt->entities = array();
	$idx = 0;
	foreach($entityResult['entities'] as $value) {
		$entity = new stdClass();
		$entity->key = $value['key'];
		$entity->items = array();
		$idx2 = 0;
		foreach($value['items'] as $item) {
			$newItem = new stdClass();
			$newItem->itemid = $item['itemid'];
			$newItem->itemtype = $item['itemtype'];
			$newItem->annotation = $item['annotation'];
			$newItem->bdata = $item['bdata'];
			$entity->items[$idx2++] = $newItem;
		}
		$rslt->entities[$idx++] = $entity;
	}
	    
    return $rslt;
}

function getEntityFromJsonCompatible($jsonEntity) {
	$entity = new Entity();
	$entity->setKey = $jsonEntity->key;
	foreach($jsonEntity->items as $value) {
		$item = new EntityItem();
		$item->setItemId($value->itemid);
		$item->setItemType($value->itemtype);
		$item->setAnnotation($value->annotation);
		$item->setBdata($value->bdata);
		$entity->addItem($item);
	}
	
	return $entity;	
}

$api = $_POST['api'];
$api = str_replace("\\", "", $api);

$rslt = new stdClass();
$rslt->errorMessage = "";

try {
	$params = json_decode(urldecode($api));
	
	$method = $params->method;
	if ($method == 'logon') {
		$un = $params->args->username;
		$pw = $params->args->password;
		$token = logon($un, $pw);
		$rslt->retval = $token;	
	}
	elseif ($method == "registerNewUser" ) {
		$un = $params->args->username;
		$pw = $params->args->password;
		$token = registerNewUser($un, $pw);
		$rslt->retval = $token;	
	}
	elseif ($method == "changePassword") {
		$newPw = $params->args->newPassword;
		$token = $params->args->token;
		
		$nRslt = changePassword($token, $newPw);
		$rslt->retval = $nRslt;
	}
	elseif ($method == "deleteAccount") {
		$un = $params->args->username;
		$pw = $params->args->password;
		$token = $params->args->token;
		
		$nRslt = deleteAccount($token, $un, $pw);
		$rslt->result = $nRslt;
	}
	elseif ($method == "deleteEntity") {
		# not implemented yet
		$rslt->retval = -1;
		$rslt->errorMessage = "not implemented";
	}
	elseif ($method == "getByDate") {
		$token = $params->args->token;
		$startDate = $params->args->startDate;
		$endDate = $params->args->endDate;
		$searchKeyResult = getByDate($token, $startDate, $endDate);
		
		$rslt->retval = getSearchKeyResultToJsonCompatible($searchKeyResult);
	}
	elseif ($method == "getByType") {
		$token = $params->args->token;
		$type = $params->args->type;
		$searchKeyResult = getByType($token, $type);
		
		$rslt->retval = getSearchKeyResultToJsonCompatible($searchKeyResult);
	}
	elseif ($method == "getAllKeys" ) {
		$token = $params->args->token;
		$searchKeyResult = getAllKeys($token);
		
		$rslt->retval = getSearchKeyResultToJsonCompatible($searchKeyResult);
	}
	elseif ($method == "getSharedKeys" ) {
		$token = $params->args->token;
		$searchKeyResult = getSharedKeys($token);
		
		$rslt->retval = getSearchKeyResultToJsonCompatible($searchKeyResult);
	}
	elseif ($method == "getEntity" ) {
		$token = $params->args->token;
		$key = $params->args->key;
		$entityResult = getEntity($token, $key);
		
		$rslt->retval = getEntityResultToJsonCompatible($entityResult);
	} 
	elseif ($method == "getSharedEntity" ) {
		$token = $params->args->token;
		$key = $params->args->key;
		$fromShareWithUsername = $params->args->fromShareWithUsername;

		$entityResult = new EntityResult();
		$entityResult->setResult(-1);
		$entityResult->setErrorMessage("not implemented");
		$rslt->retval = getEntityResultToJsonCompatible($entityResult);
	} 
	elseif ($method == "shareEntity") {
		$token = $params->args->token;
		$pEntity = $params->args->entity;
		$toShareWithUsername = $params->args->toShareWithUsername;
		
		$entity = getEntityFromJsonCompatible($pEntity);
		$nResult = shareEntity($token, $entity, $toShareWithUsername); 
		
		$rslt->retval = $nRslt;
		
	} elseif ($method == "storeEntity") {
		$token = $params->args->token;
		$pEntity = $params->args->entity;
		$entity = getEntityFromJsonCompatible($pEntity);
		$nResult = storeEntity($token, $entity); 
		
		$rslt->retval = $nRslt;
	}
	else {
		$rslt->retval = "not set";
	}
} catch(Exception $ex) {
	$rslt->errorMessage = "exception";
}

echo json_encode($rslt);
?>