<?php

class Entity {
    private $key;
    private $items;

    public function Entity() {
        $this->key = 'notset';
        $this->items = array();
    }

    public function addItem($entityItem) {
	$idx = count($this->items);
	$this->items[$idx] = $entityItem;
    }

    public function getKey() {
	return $this->key;
    }

    public function setKey($value) {
	$this->key = $value;
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

}

?>
