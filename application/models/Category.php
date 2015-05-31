<?php

class Application_Model_Category extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name= 'category';
    
    public function getCategorys()
    {
        $select = $this->select()->setIntegrityCheck(false);
        $select->from('category');
        $select->joinLeft('size', 'sizeId=categorySize');
        
        return $this->fetchAll($select)->toArray();
    }
    
    public function addCategory($category)
    {
        return $this->insert($category);
    }
    
    public function editCategory($categoryId, $category)
    {
        return $this->update($category, "categoryId=$categoryId");
    }
    
    public function deleteCategory($categoryId)
    {
        return $this->delete("categoryId=$categoryId");
    }

    public function getCategorysByQuery($query)
    {
        
        $select = $this->select()->distinct(true);
        $select->from('category', array('data'=>'categoryId', 'value'=>'categoryName'));
        $select->where("categoryName LIKE '%$query%'");
        
        return $this->fetchAll($select)->toArray();
    }
    
}
