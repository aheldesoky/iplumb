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

    public function getCategorysByQuery($query, $import = false)
    {
        if($import){
            $select = $this->select()->setIntegrityCheck(false)->distinct(true);
            $select->from(array('category', 'size'), 
                          array('data'  =>  'categoryId', 
                                'value' =>  new Zend_Db_Expr ('CONCAT_WS(" ",categoryName,sizeValue)')
                    ));
            $select->joinLeft('size', 'sizeId=categorySize', array());
            $select->where("categoryName LIKE '%$query%'");
            //echo $select->__toString();die;
        } else {
            $select = $this->select()->distinct(true);
            $select->from('category', array('data'=>'categoryName', 'value'=>'categoryName'));
            $select->where("categoryName LIKE '%$query%'");
        }
        return $this->fetchAll($select)->toArray();
    }
    
}
