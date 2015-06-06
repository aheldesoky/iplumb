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
    
    public function getCategorysWithDataByQuery($query)
    {
        $select = $this->select()->setIntegrityCheck(false);
        $select->from( array('c'=>'category') , array('data'=>'categoryId', 'value'=>'categoryName') );
        $select->joinLeft(array('ic' => 'importCategory'), 'c.categoryId=ic.categoryId', array(
                'categoryQuantity'  => new Zend_Db_Expr('SUM(CASE WHEN ic.categoryQuantity IS NOT NULL THEN ic.categoryQuantity ELSE 0 END)'), 
                'categorySellPrice' => new Zend_Db_Expr('CASE WHEN ic.categorySellPrice IS NOT NULL THEN ic.categorySellPrice ELSE 0 END')
        ));
        $select->joinLeft(array('i' => 'import')  , 'i.importId=ic.importId', array('importDate'));
        $select->where("categoryName LIKE '%$query%'");
        $select->group(new Zend_Db_Expr('c.categoryId, i.importId'));
        $select->order(new Zend_Db_Expr('c.categoryId, i.importDate DESC'));
        //$query1 = $select->__toString();//die;
        $result = $this->fetchAll($select)->toArray();
        
        if(count($result)){
            $category_arr = array();
            $current_category = $result[0]['data'];
            $category_arr[$current_category]['categoryQuantity'] = 0;
            foreach($result as $key => $item)
            {
                if($current_category == $item['data']) {
                    $category_arr[$current_category]['categoryQuantity'] += $item['categoryQuantity'];
                } else {
                    $current_category = $item['data'];
                    $category_arr[$current_category]['categoryQuantity'] = $item['categoryQuantity'];
                }
                $category_arr[$item['data']][] = $item;
            }
            ksort($category_arr, SORT_NUMERIC);
            //echo '<pre>';print_r($category_arr);die;
            
            $categories = array();
            foreach($category_arr as $key => $item)
            {
                $item[0]['categoryQuantity'] = $item['categoryQuantity'];
                $categories[] = $item[0];
            }
            //echo '<pre>';print_r($categories);die;
            
            return $categories;
        } else {
            return array();
        }
    }
}
