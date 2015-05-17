<?php

class Application_Model_Role extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name= 'role';
    
    public function getRoles()
    {
        return $this->fetchAll()->toArray();
    }
    
    public function addRole($role)
    {
        return $this->insert($role);
    }
    
    public function editRole($roleId, $role)
    {
        return $this->update($role, "roleId=$roleId");
    }
    
    public function deleteRole($roleId)
    {
        return $this->delete("roleId=$roleId");
    }

}

