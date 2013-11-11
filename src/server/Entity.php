<?php

class Entity {
    private $key;
    private $items;

    public function __construct() {
        $this->key = 'notset';
        $this->items = array();
    }

    public function addItem($entityItem) {
		$idx = count($this->items);
		$arr = $entityItem->getData();
		$this->items[$idx] = $entityItem;
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

    public function getData() {
        $retval = array();
        $retval['key'] = $this->key;
        
        $dataItems = array();
        $count = 0;
        foreach($this->items as $key => $value) {
        	$dataItems[$key] = $value->getData();
        }
        $retval['items'] = $dataItems;

		return $retval;
    }

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
    
}

?>
