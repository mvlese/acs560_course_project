<?php

class EntityItem {

    private $itemid;
    private $itemtype;
    private $annotation;
    private $bdata;

    public function __construct() {
        $this->itemid = -1;
        $this->itemtype = 'text';
        $this->annotation = '';
        $this->bdata = '';
    }

    public function setItemId($value) {
    	$this->itemid = $value;
    }
    
    public function setItemType($value) {
    	$this->itemtype = $value;
    }
    
    public function setAnnotation($value) {
    	$this->annotation = $value;
    }
    
    # Set base-64 encoded data.
    public function setBdata($value) {
    	$this->bdata = $value;
    }
    
    public function getData() {
        $retval = array();
        $retval['itemid'] = $this->itemid;
        $retval['itemtype'] = $this->itemtype;
        $retval['annotation'] = $this->annotation;
        $retval['bdata'] = $this->bdata;

		return $retval;
    }
    
    public static function getItemFromData($data) {
		$item = new EntityItem();
        $item->setItemId($data['itemid']);
        $item->setItemType($data['itemtype']);
        $item->setAnnotation($data['annotation']);
        $item->setBdata($data['bdata']);
    	
    	return $item;
    }

}

?>
