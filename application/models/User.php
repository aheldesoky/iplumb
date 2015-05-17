<?php

class Application_Model_User extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name= 'user';
    
    public function getUsers()
    {
        $select = $this->select()->setIntegrityCheck(false);
        $select->from(array('u'=>'user'));
        $select->join(array('r'=>'role'), 'u.userRole=r.roleId');
        return $this->fetchAll($select)->toArray();
    }
    
    public function addUser($user)
    {
        return $this->insert($user);
    }
    
    public function editUser($userId, $user)
    {
        return $this->update($user, "userId=$userId");
    }
    
    public function deleteUser($userId)
    {
        return $this->delete("userId=$userId");
    }
}

