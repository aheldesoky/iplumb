<?php

class Application_Model_Customer extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name= 'customer';
    
    public function addCustomer($customer)
    {
        return $this->insert($customer);
    }
    
    public function getCustomerById($customerId)
    {
        return $this->fetchRow("customerId=$customerId")->toArray();
    }
    
    

}

