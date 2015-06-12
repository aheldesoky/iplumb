<?php

class Application_Model_Customer extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name= 'customer';
    
    public function addCustomer($customer)
    {
        return $this->insert($customer);
    }
    
    public function editCustomer($customerId, $customer)
    {
        return $this->update($customer, "customerId=$customerId");
    }
    
    public function getCustomerById($customerId)
    {
        return $this->fetchRow("customerId=$customerId")->toArray();
    }
    
    

}

