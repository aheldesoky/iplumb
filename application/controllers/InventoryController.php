<?php

class InventoryController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $importCategoryModel = new Application_Model_ImportCategory();
        $inventory = $importCategoryModel->getInventory();
        //echo '<pre>';print_r($inventory);die;
        
        $this->view->inventory = $inventory;
    }


}