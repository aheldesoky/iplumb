<?php

class Application_Model_SaleCategory extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name= 'saleCategory';
    
    public function addSaleCategory($category)
    {
        return $this->insert($category);
    }
    
    public function getSaleCategories($saleId)
    {
        $select = $this->select()->setIntegrityCheck(false);
        $select->from(array('sc' => 'saleCategory'));
        $select->join(array('c' => 'category'), 'c.categoryId=sc.categoryId');
        $select->where("sc.saleId=$saleId");
        
        return $this->fetchAll($select)->toArray();
    }
    
    public function deleteSaleCategories($saleId)
    {
        return $this->delete("saleId=$saleId");
    }
    

}

