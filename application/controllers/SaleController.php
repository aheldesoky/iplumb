<?php

class SaleController extends Zend_Controller_Action
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
        $saleForm = new Application_Form_Sale();
        
        $this->view->form = $saleForm;
    }

    public function addAction()
    {
        //print_r($this->getRequest()->getPost());die;
        $saleForm = new Application_Form_Sale();
        
        if($this->getRequest()->isPost()){
            $data = $this->getRequest()->getPost();
            //print_r($data);die;
            unset($data['submit']);
            
            if($saleForm->isValid($data)){
                $saleModel = new Application_Model_Sale();
                $saleCategoryModel = new Application_Model_SaleCategory();
                $customerModel = new Application_Model_Customer();
                
                $categories = json_decode(stripcslashes($data['saleCategories']), true);
                $customer   = json_decode(stripcslashes($data['saleCustomer']), true);
                unset($data['saleCategories']);
                unset($data['saleCustomer']);
                //print_r($categories);die;
                
                $customerId = $customerModel->addCustomer($customer);
                $data['saleCustomer'] = $customerId;
                $saleId = $saleModel->addSale($data);
                
                foreach ($categories as &$category){
                    $category['saleId'] = $saleId;
                    $saleCategoryModel->addSaleCategory($category);
                }
                //print_r($categories);die;
                
                $this->getHelper('json')->sendJson( array('redirectUrl' => $this->view->baseUrl("/sale/bill/id/$saleId") ) );
                
            } else {
                $this->getHelper('json')->sendJson( array('errors' => $saleForm->getMessages()) );
            }
        }
        
    }

    public function billAction()
    {
        $saleId = $this->getRequest()->getParam('id');
        
        $saleModel = new Application_Model_Sale();
        $saleCategoryModel = new Application_Model_SaleCategory();
        $customerModel = new Application_Model_Customer();
        
        $sale = $saleModel->getSaleById($saleId);
        $categories = $saleCategoryModel->getSaleCategories($saleId);
        $customer = $customerModel->getCustomerById($sale['saleCustomer']);
        
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();        
        $this->view->user = $userInfo->userFullname;
        $this->view->sale = $sale;
        $this->view->categories = $categories;
        $this->view->customer = $customer;
        
        $this->_helper->layout()->disableLayout();
    }


}
