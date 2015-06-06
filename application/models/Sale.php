<?php

class Application_Model_Sale extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name= 'sale';
    
    public function getSales($page, $salesPerPage)
    {
        $select = $this->select()->setIntegrityCheck(false);
        $select->from(array('s'=>'sale'));
        $select->joinLeft(array('c'=>'customer'), 's.saleCustomer=c.customerId');
        
        $select->joinLeft(array('sc'=>'saleCategory'), 'sc.saleId=s.saleId', 
                array('totalSellPrice' => new Zend_Db_Expr('SUM(CASE WHEN sc.categorySellPrice IS NOT NULL THEN sc.categorySellPrice * sc.categoryQuantity ELSE 0 END)')
        ));
        $select->group('s.saleId');
        $select->order('s.saleDate ASC');
        $select->limitPage($page, $salesPerPage);
        
        return $this->fetchAll($select)->toArray();
    }
    
    public function countSales()
    {
        $select = $this->select()->from("sale", array("totalSales"=>"COUNT(*)"));
        $result = $this->fetchRow($select)->toArray();
        return $result['totalSales'];
    }
    
    public function addSale($sale)
    {
        return $this->insert($sale);
    }
    
    public function getSaleById($saleId)
    {
        return $this->fetchRow("saleId=$saleId")->toArray();
    }
    
    public function deleteSale($saleId)
    {
        return $this->delete("saleId=$saleId");
    }

}

