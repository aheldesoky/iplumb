<?php

class InventoryController extends Zend_Controller_Action
{

    public function init()
    {
        $authorization = Zend_Auth::getInstance();
        if(!$authorization->hasIdentity()) {
            $this->redirect('/auth/login');
        }
    }

    public function indexAction()
    {
        $importCategoryModel = new Application_Model_ImportCategory();
        $inventory = $importCategoryModel->getInventory();
        //echo '<pre>';print_r($inventory);die;
        
        $this->view->inventory = $inventory;
    }


}