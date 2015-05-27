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
    
    public function getInventory()
    {
        $select = $this->select()->setIntegrityCheck(false);
        $select->from(array('c'=>'category'));
        $select->joinLeft(array('ic' => 'importCategory'), 'c.categoryId=ic.categoryId', array(
                'categoryQuantity'  => new Zend_Db_Expr('SUM(CASE WHEN ic.categoryQuantity IS NOT NULL THEN ic.categoryQuantity ELSE 0 END)'), 
                'categorySellPrice' => new Zend_Db_Expr('CASE WHEN ic.categorySellPrice IS NOT NULL THEN ic.categorySellPrice ELSE 0 END')
        ));
        $select->joinLeft(array('i' => 'import')  , 'i.importId=ic.importId', array('importDate'));
        $select->group(new Zend_Db_Expr('c.categoryId, i.importId'));
        $select->order(new Zend_Db_Expr('c.categoryId, i.importDate DESC'));
        //$query1 = $select->__toString();//die;
        $result1 = $this->fetchAll($select)->toArray();
        
        $category_arr = array();
        $current_category = $result1[0]['categoryId'];
        $category_arr[$current_category]['categoryQuantity'] = 0;
        foreach($result1 as $key => $item)
        {
            if($current_category == $item['categoryId']) {
                $category_arr[$current_category]['categoryQuantity'] += $item['categoryQuantity'];
            } else {
                $current_category = $item['categoryId'];
                $category_arr[$current_category]['categoryQuantity'] = $item['categoryQuantity'];
            }
            $category_arr[$item['categoryId']][] = $item;
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
