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
        $selectImport = $this->select()->setIntegrityCheck(false);
        $selectImport->from( array('c'=>'category') , array('data'=>'categoryId', 'value'=>'categoryName') );
        $selectImport->joinLeft(array('ic' => 'importCategory'), 'c.categoryId=ic.categoryId', array(
                'categoryQuantity'  => new Zend_Db_Expr('SUM(CASE WHEN ic.categoryQuantity IS NOT NULL THEN ic.categoryQuantity ELSE 0 END)'), 
                'categorySellPrice' => new Zend_Db_Expr('CASE WHEN ic.categorySellPrice IS NOT NULL THEN ic.categorySellPrice ELSE 0 END')
        ));
        $selectImport->joinLeft(array('i' => 'import')  , 'i.importId=ic.importId', array('importDate'));
        $selectImport->where("categoryName LIKE '%$query%'");
        $selectImport->group(new Zend_Db_Expr('c.categoryId, i.importId'));
        $selectImport->order(new Zend_Db_Expr('c.categoryId, i.importDate DESC'));
        //$queryImport = $selectImport->__toString();//die;
        $resultImport = $this->fetchAll($selectImport)->toArray();
        
        $selectSale = $this->select()->setIntegrityCheck(false);
        $selectSale->from(array('c'=>'category'));
        $selectSale->joinLeft(array('sc' => 'saleCategory'), 'c.categoryId=sc.categoryId', array(
                'categoryQuantity'  => new Zend_Db_Expr('SUM(CASE WHEN sc.categoryQuantity IS NOT NULL THEN sc.categoryQuantity ELSE 0 END)'), 
        ));
        $selectSale->where("categoryName LIKE '%$query%'");
        $selectSale->group(new Zend_Db_Expr('c.categoryId'));
        $selectSale->order(new Zend_Db_Expr('c.categoryId'));
        //$querySale = $selectSale->__toString();//die;
        $resultSale = $this->fetchAll($selectSale)->toArray();
        //echo '<pre>';print_r($resultSale);die;
        
        if(count($resultImport)){
            $category_arr = array();
            $current_category = $resultImport[0]['data'];
            $category_arr[$current_category]['categoryQuantity'] = 0;
            foreach($resultImport as $key => $item)
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
            $i = 0;
            foreach($category_arr as $key => $item)
            {
                $item[0]['categoryQuantity'] = $item['categoryQuantity'] - $resultSale[$i++]['categoryQuantity'];
                $categories[] = $item[0];
            }
            //echo '<pre>';print_r($categories);die;
            
            return $categories;
        } else {
            return array();
        }
    }
}
