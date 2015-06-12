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
        $saleModel = new Application_Model_Sale();
        $currentPage = $this->getRequest()->getParam('page');
        $currentPage = ($currentPage) ? $currentPage : 1;
        $salesPerPage = 10;
        
        $this->view->sales = $saleModel->getSales($currentPage, $salesPerPage);
        
        $this->view->totalPages = ceil($saleModel->countSales() / $salesPerPage);
        //echo $this->view->totalPages;//die;
        //echo '<pre>';print_r($this->view->sales);die;
        $this->view->currentPage = $currentPage;
        
    }

    public function addAction()
    {
        //print_r($this->getRequest()->getPost());die;
        $saleForm = new Application_Form_Sale();
        $saleForm->setAction("/sale/add");
        
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
        } else {
            $this->view->form = $saleForm;
        }
        
    }
    
    public function editAction()
    {
        $saleId = $this->getRequest()->getParam('id');
        
        $saleModel = new Application_Model_Sale();
        $saleCategoryModel = new Application_Model_SaleCategory();
        $customerModel = new Application_Model_Customer();
        
        $sale = $saleModel->getSaleById($saleId);
        
        $saleForm = new Application_Form_Sale();
        $saleForm->setAction("/sale/edit/id/$saleId");
        
        $translate = Zend_Registry::get('Zend_Translate');
        $saleForm->getElement('submit')
                   ->setLabel($translate->translate('Edit Sale'))
                   ->setAttrib('class', 'btn btn-lg btn-warning');
        
        if($this->getRequest()->isPost()){
            $data = $this->getRequest()->getPost();
            //print_r($data);die;
            unset($data['submit']);
            
            if($saleForm->isValid($data)){
                
                $categories = json_decode(stripcslashes($data['saleCategories']), true);
                $customer   = json_decode(stripcslashes($data['saleCustomer']), true);
                unset($data['saleCategories']);
                unset($data['saleCustomer']);
                //print_r($categories);die;
                
                $customerModel->editCustomer($sale['saleCustomer'], $customer);
                //$data['saleCustomer'] = $customerId;
                $saleModel->editSale($saleId, $data);
                
                $saleCategoryModel->deleteSaleCategories($saleId);
                foreach ($categories as &$category){
                    $category['saleId'] = $saleId;
                    $saleCategoryModel->addSaleCategory($category);
                }
                //print_r($categories);die;
                
                $this->getHelper('json')->sendJson( array('redirectUrl' => $this->view->baseUrl("/sale/bill/id/$saleId") ) );
                
            } else {
                $this->getHelper('json')->sendJson( array('errors' => $saleForm->getMessages()) );
            }
        } else {
            $saleForm->populate($sale);
            $this->view->categories = $saleCategoryModel->getSaleCategories($saleId);
            $this->view->sale = $sale;
            $this->view->form = $saleForm;
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
        
        $totalDue = 0;
        foreach ($categories as $category){
            $totalDue += $category['categoryQuantity'] * $category['categorySellPrice'];
        }
        
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();        
        $this->view->user = $userInfo->userFullname;
        $this->view->sale = $sale;
        $this->view->totalDue = $totalDue;
        $this->view->categories = $categories;
        $this->view->customer = $customer;
        
        $this->_helper->layout()->disableLayout();
    }

    public function deleteAction()
    {
        $saleId = $this->getRequest()->getParam('id');
        $saleModel = new Application_Model_Sale();
        $saleCategoryModel = new Application_Model_SaleCategory();
        
        $saleModel->deleteSale($saleId);
        $saleCategoryModel->deleteSaleCategories($saleId);
        
        $this->redirect('/sale');
    }

}
