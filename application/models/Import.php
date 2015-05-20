<?php

class Application_Model_Import extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name= 'import';
    
    public function getImports($page, $importsPerPage)
    {
        $select = $this->select()->setIntegrityCheck(false);
        $select->from(array('i'=>'import'));
        $select->joinLeft(array('s'=>'supplier'), 's.supplierId=i.importSupplier');
        
        $select->joinLeft(array('ic'=>'importCategory'), 'ic.importId=i.importId', 
                array('totalBuyPrice' => new Zend_Db_Expr('SUM(CASE WHEN ic.categoryBuyPrice IS NOT NULL THEN ic.categoryBuyPrice * ic.categoryQuantity ELSE 0 END)'), 
                      'totalSellPrice' => new Zend_Db_Expr('SUM(CASE WHEN ic.categorySellPrice IS NOT NULL THEN ic.categorySellPrice * ic.categoryQuantity ELSE 0 END)')
        ));
        $select->group('i.importId');
        $select->limitPage($page, $importsPerPage);
        
        return $this->fetchAll($select)->toArray();
    }
    
    public function countImports()
    {
        $select = $this->select()->from("import", array("totalImports"=>"COUNT(*)"));
        $result = $this->fetchRow($select)->toArray();
        return $result['totalImports'];
    }
    
    public function getImportById($importId)
    {
        $select = $this->select()->setIntegrityCheck(false);
        $select->from(array('i'=>'import'));
        $select->joinLeft(array('s'=>'supplier'), 's.supplierId=i.importSupplier');
        
        $select->joinLeft(array('ic'=>'importCategory'), 'ic.importId=i.importId', 
                array('totalBuyPrice' => new Zend_Db_Expr('SUM(CASE WHEN ic.categoryBuyPrice IS NOT NULL THEN ic.categoryBuyPrice * ic.categoryQuantity ELSE 0 END)'), 
                      'totalSellPrice' => new Zend_Db_Expr('SUM(CASE WHEN ic.categorySellPrice IS NOT NULL THEN ic.categorySellPrice * ic.categoryQuantity ELSE 0 END)')
        ));
        
        $select->where("i.importId=$importId");
        $select->group('i.importId');
        
        return $this->fetchRow($select)->toArray();
    }
    
    public function addImport($import)
    {
        return $this->insert($import);
    }
    
    public function editImport($importId, $import)
    {
        return $this->update($import, "importId=$importId");
    }
    
    public function deleteImport($importId)
    {
        return $this->delete("importId=$importId");
    }
    
}

