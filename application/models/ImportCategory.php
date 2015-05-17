<?php

class Application_Model_ImportCategory extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name= 'importCategory';
    
    public function getImportCategories($importId)
    {
        $select = $this->select()->setIntegrityCheck(false);
        $select->from(array('ic'=>'importCategory'));
        $select->joinLeft(array('c'=>'category'), 'c.categoryId=ic.categoryId');
        $select->joinLeft(array('s'=>'size'), 's.sizeId=c.categorySize');
        $select->joinLeft(array('i'=>'import'), 'i.importId=ic.importId');
        $select->joinLeft(array('sp'=>'supplier'), 'sp.supplierId=i.importSupplier');
        $select->where("i.importId=$importId");
        return $this->fetchAll($select)->toArray();
    }
    
    public function addImportCategories($importCategories)
    {
        return $this->insert($importCategories);
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
