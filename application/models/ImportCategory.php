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
        //$select->joinLeft(array('s'=>'size'), 's.sizeId=c.categorySize');
        $select->joinLeft(array('i'=>'import'), 'i.importId=ic.importId');
        $select->joinLeft(array('sp'=>'supplier'), 'sp.supplierId=i.importSupplier');
        $select->where("i.importId=$importId");
        return $this->fetchAll($select)->toArray();
    }
    
    public function getInventory()
    {
        $selectImport = $this->select()->setIntegrityCheck(false);
        $selectImport->from(array('c'=>'category'));
        $selectImport->joinLeft(array('ic' => 'importCategory'), 'c.categoryId=ic.categoryId', array(
                'categoryQuantity'  => new Zend_Db_Expr('SUM(CASE WHEN ic.categoryQuantity IS NOT NULL THEN ic.categoryQuantity ELSE 0 END)'), 
                'categorySellPrice' => new Zend_Db_Expr('CASE WHEN ic.categorySellPrice IS NOT NULL THEN ic.categorySellPrice ELSE 0 END')
        ));
        $selectImport->joinLeft(array('i' => 'import')  , 'i.importId=ic.importId', array('importDate'));
        $selectImport->group(new Zend_Db_Expr('c.categoryId, i.importId'));
        $selectImport->order(new Zend_Db_Expr('c.categoryId, i.importDate DESC'));
        //$queryImport = $selectImport->__toString();//die;
        $resultImport = $this->fetchAll($selectImport)->toArray();
        
        $selectSale = $this->select()->setIntegrityCheck(false);
        $selectSale->from(array('c'=>'category'));
        $selectSale->joinLeft(array('sc' => 'saleCategory'), 'c.categoryId=sc.categoryId', array(
                'categoryQuantity'  => new Zend_Db_Expr('SUM(CASE WHEN sc.categoryQuantity IS NOT NULL THEN sc.categoryQuantity ELSE 0 END)'), 
        ));
        $selectSale->group(new Zend_Db_Expr('c.categoryId'));
        $selectSale->order(new Zend_Db_Expr('c.categoryId'));
        //$querySale = $selectSale->__toString();//die;
        $resultSale = $this->fetchAll($selectSale)->toArray();
        //echo '<pre>';print_r($resultSale);die;
        
        $category_arr = array();
        $current_category = $resultImport[0]['categoryId'];
        $category_arr[$current_category]['categoryQuantity'] = 0;
        foreach($resultImport as $key => $item)
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
        $i = 0;
        foreach($category_arr as $key => $item)
        {
            $item[0]['categoryQuantity'] = $item['categoryQuantity'] - $resultSale[$i++]['categoryQuantity'];
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
    
    public function deleteImportCategories($importId)
    {
        return $this->delete("importId=$importId");
    }

}
