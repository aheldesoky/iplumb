<?php

class Application_Model_Sale extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name= 'sale';
    
    public function addSale($sale)
    {
        return $this->insert($sale);
    }
    
    public function getSaleById($saleId)
    {
        return $this->fetchRow("saleId=$saleId")->toArray();
    }
    

}

