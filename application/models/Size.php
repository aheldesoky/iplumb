<?php

class Application_Model_Size extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name= 'size';
    
    public function getSizes()
    {
        return $this->fetchAll()->toArray();
    }
    
    public function addSize($size)
    {
        return $this->insert($size);
    }
    
    public function editSize($sizeId, $size)
    {
        return $this->update($size, "sizeId=$sizeId");
    }
    
    public function deleteSize($sizeId)
    {
        return $this->delete("sizeId=$sizeId");
    }

}

