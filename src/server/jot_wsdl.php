<?php
require_once "lib/nusoap.php";
require_once "methods2.php";

class JotWsdl {

	private static function registerMethods($server) {
		$server->register('changePassword',
			array('token' => 'xsd:string', 'newPassword' => 'xsd:string'),
			array('retval' => 'xsd:int'));
		
		$server->register('deleteAccount',
			array('token' => 'xsd:string', 'username' => 'xsd:string', 'password' => 'xsd:string'),
			array('retval' => 'xsd:int'));
		
		$server->register('deleteEntity',
			array('token' => 'xsd:string', 'entity' => 'tns:Entity'),
			array('retval' => 'xsd:int'));
		
		$server->register('getByDate',
			array('token' => 'xsd:string', 'date' => 'xsd:string'),
			array('retval' => 'tns:EntityResult'));
	
		$server->register('getByType',
			array('token' => 'xsd:string', 'type' => 'xsd:string'),
			array('retval' => 'tns:EntityResult'));
		
		$server->register('getEntity',
			array('token' => 'xsd:string', 'entity' => 'tns:Entity'),
			array('retval' => 'tns:EntityResult'));
		
		$server->register('getSharedEntity',
			array('token' => 'xsd:string', 'fromShareWithUsername' => 'xsd:string'),
			array('retval' => 'tns:EntityResult'));
		
		$server->register('logon',
			array('username' => 'xsd:string', 'password' => 'xsd:string'),
			array('retval' => 'xsd:string'));
		
		$server->register('register',
			array('username' => 'xsd:string', 'password' => 'xsd:string'),
			array('retval' => 'xsd:int'), 'urn:jot');
		
		$server->register('shareEntity',
			array('token' => 'xsd:string', 'entity' => 'tns:Entity', 'toShareWithUsername' => 'xsd:string'),
			array('retval' => 'xsd:int'));
		
		$server->register('storeEntity',
			array('token' => 'xsd:string', 'entity' => 'tns:Entity'),
			array('retval' => 'xsd:int'));
	}


	public static function init($server) {
		$server->wsdl->addComplexType('EntityItem', 'entityItem', 'struct', 'all', '',
		array(
			'itemid' => array('name' => 'itemid', 'type' => 'xsd:int'),
			'itemtype' => array('name' => 'itemtype', 'type' => 'xsd:string'),
			'annotation' => array('name' => 'annotation', 'type' => 'xsd:string', 'nillable' => 'true'),
			'bdata' => array('name' => 'bdata', 'type' => 'xsd:string', 'nillable' => 'true')));
		
		$server->wsdl->addComplexType('EntityItems', 'entityItems', 'array', '', 'SOAP-ENC:Array',
			array(), 
			array(
				array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:EntityItem[]'),
				), 'tns:EntityItem'
		);
	
		$server->wsdl->addComplexType('Entity', 'entity', 'struct', 'all', '',
		array(
			'key' => array('name' => 'key', 'type' => 'xsd:string'),
			'items' => array('name' => 'items', 'type' => 'tns:EntityItems')));

		$server->wsdl->addComplexType('EntityList', 'entityList', 'array', '', 'SOAP-ENC:Array',
			array(), 
			array(
				array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:Entity[]'),
				),'tns:Entity'
		);
	
		$server->wsdl->addComplexType('EntityResult', 'entityresult', 'struct', 'all', '',
		array(
			'result' => array('name' => 'result', 'type' => 'xsd:int'),
			'errorMessage' => array('name' => 'errorMessage', 'type' => 'xsd:string'),
			'entities' => array('name' => 'entities', 'type' => 'tns:EntityList')));


		JotWsdl::registerMethods($server);
	}
}

?>