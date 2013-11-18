<?php

class Entity {
    private $key;
    private $items;
    private $modified;
    private $items_size;

    public function __construct() {
        $this->key = 'notset';
	 $this->modified = 'notset';
        $this->items_size = 0;
        $this->items = array();
    }

    public function addItem($entityItem) {
		$idx = count($this->items);
		$this->items[$idx] = $entityItem;
		$this->items_size += $entityItem->getSize();
    }

    public function getKey() {
		return $this->key;
    }

    public function setKey($value) {
		$this->key = $value;
    }

    public function getItems() {
		return $this->items;
    }

    public function setItemsSize($items_size) {
	$this->items_size = $items_size;
    }

    public function getModified() {
	return $this->modified;
    }

    public function setModified($modified) {
	$this->modified = $modified;
    }

    public function getData() {
        $retval = array();
        $retval['key'] = $this->key;
        $retval['item_memory_bytes'] = $this->items_size;
	 $retval['modified'] = $this->modified;
        $dataItems = array();
        $count = 0;
	$idx = 0;
        foreach($this->items as $key => $value) {
        	$dataItems[$idx++] = $value->getData();
        }
        $retval['items'] = $dataItems;
		return $retval;
    }
/*
    public static function getEntityFromData($data) {
    	$entity = new Entity();
    	$entity->setKey($data['key']);
		$items = $data['items'];
    	foreach($items as $key => $value) {
    		# Should only be one $value.
			foreach($value as $key1 => $value1) {
	    			$item = EntityItem::getItemFromData($value1);
	    			$entity->addItem($item);
			}
    	}	
    	
	return $entity;
    }
 */   

    public static function getEntityFromData($data) {
		$entity = new Entity();
		$entity->setKey($data['key']);
		$items = $data['items'];
		foreach($items as $key1 => $value1) {
			$item = EntityItem::getItemFromData($value1);
			$entity->addItem($item);
		}
		return $entity;
    }
    
}

?>
