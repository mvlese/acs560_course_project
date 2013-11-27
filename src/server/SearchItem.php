<?php
/*
			'key' => array('name' => 'key', 'type' => 'xsd:string'),
			'owner' => array('name' => 'owner', 'type' => 'xsd:string'),
			'item_memory_bytes' => array('name' => 'item_memory_bytes', 'type' => 'xsd:int'),
			'modified' => array('name' => 'modified', 'type' => 'xsd:string')));
*/

class SearchItem {
	private $key;
	private $owner;
	private $itemSize;
	private $modified;
	
	public function __construct() {
		$this->key = '';
		$this->owner = 'self';
		$this->itemSize = 0;
		$this->modified = '';
				
	}
	
	public function setKey($key) {
		$this->key = $key;
	}
	
	public function setOwner($owner) {
		$this->owner = $owner;
	}
	
	public function setItemSize($itemSize) {
		$this->itemSize = $itemSize;
	}
	
	public function setModified($modified) {
		$this->modified = $modified;
	}
	
	public function getData() {
		$retval = array();
		$retval['key'] = $this->key;
		$retval['owner'] = $this->owner;
		$retval['item_memory_bytes'] = $this->itemSize;
		$retval['modified'] = $this->modified;
		
		return $retval;
	}
}
?>