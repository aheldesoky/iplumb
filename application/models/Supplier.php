<?php

class Application_Model_Supplier extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name= 'supplier';
    
    public function getSuppliers()
    {
        return $this->fetchAll()->toArray();
    }
    
    public function addSupplier($supplier)
    {
        return $this->insert($supplier);
    }
    
    public function editSupplier($supplierId, $supplier)
    {
        return $this->update($supplier, "supplierId=$supplierId");
    }
    
    public function deleteSupplier($supplierId)
    {
        return $this->delete("supplierId=$supplierId");
    }
}

