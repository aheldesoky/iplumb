<?php

class ImportController extends Zend_Controller_Action
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
        $importModel = new Application_Model_Import();
        $currentPage = $this->getRequest()->getParam('page');
        $currentPage = ($currentPage) ? $currentPage : 1;
        $importsPerPage = 10;
        
        $this->view->imports = $importModel->getImports($currentPage, $importsPerPage);
        
        $this->view->totalPages = ceil($importModel->countImports() / $importsPerPage);
        //echo $this->view->totalPages;//die;
        //echo '<pre>';print_r($this->view->imports);die;
        $this->view->currentPage = $currentPage;
        
    }

    public function addAction()
    {
        $importForm = new Application_Form_Import();
        $importForm->setAction('/import/add');
        
        if($this->getRequest()->isPost()){
            $data = $this->getRequest()->getPost();
            //print_r($data);die;
            unset($data['submit']);
            
            if($importForm->isValid($data)){
                $importModel = new Application_Model_Import();
                $importCategoryModel = new Application_Model_ImportCategory();
                
                $categories = json_decode(stripcslashes($data['importCategories']), true);
                unset($data['importCategories']);
                //print_r($categories);die;
                $importId = $importModel->addImport($data);
                
                foreach ($categories as &$category){
                    $category['importId'] = $importId;
                    $importCategoryModel->addImportCategories($category);
                }
                //print_r($categories);die;
                
                $this->getHelper('json')->sendJson( array('redirectUrl' => $this->view->baseUrl('/import') ) );
                
            } else {
                $this->getHelper('json')->sendJson( array('errors' => $importForm->getMessages()) );
            }
        }
        
        $categoryForm = new Application_Form_Category();
        $this->view->categroyForm = $categoryForm;
        
        $this->view->form = $importForm;
    }

    public function editAction()
    {
        $importId = $this->getRequest()->getParam('id');
        
        $importModel = new Application_Model_Import();
        $importCategoryModel = new Application_Model_ImportCategory();
        
        $import = $importModel->fetchRow("importId=$importId")->toArray();
        
        $importForm = new Application_Form_Import();
        $importForm->setAction("/import/edit/id/$importId");
        
        $translate = Zend_Registry::get('Zend_Translate');
        $importForm->getElement('submit')
                   ->setLabel($translate->translate('Edit Import'))
                   ->setAttrib('class', 'btn btn-lg btn-warning');
        
        //if($import['importDiscount'] == 0)
        //    $importForm->removeElement('importDiscount');
        
        if($this->getRequest()->isPost()){
            $data = $this->getRequest()->getPost();
            //print_r($data);die;
            unset($data['submit']);
            
            if($importForm->isValid($data)){
                
                $categories = json_decode(stripcslashes($data['importCategories']), true);
                unset($data['importCategories']);
                //print_r($categories);die;
                $importModel->editImport($importId, $data);
                
                $importCategoryModel->deleteImportCategories($importId);
                foreach ($categories as &$category){
                    $category['importId'] = $importId;
                    $importCategoryModel->addImportCategories($category);
                }
                //print_r($categories);die;
                
                $this->getHelper('json')->sendJson( array('redirectUrl' => $this->view->baseUrl('/import') ) );
                
            } else {
                $this->getHelper('json')->sendJson( array('errors' => $importForm->getMessages()) );
            }
        } else {
            $importForm->populate($import);
            $this->view->categories = $importCategoryModel->getImportCategories($importId);
            $this->view->import = $import;
            $this->view->form = $importForm;
        }
    }

    public function deleteAction()
    {
        $importId = $this->getRequest()->getParam('id');
        $importModel = new Application_Model_Import();
        $importCategoryModel = new Application_Model_ImportCategory();
        
        $importModel->deleteImport($importId);
        $importCategoryModel->deleteImportCategories($importId);
        
        $this->redirect('/import');
    }

    public function ajaxAction()
    {
        $this->_helper->layout->disableLayout();
    }

    public function viewAction()
    {
        $importId = $this->getRequest()->getParam('id');
        $importModel = new Application_Model_Import();
        
        $this->view->import = $importModel->getImportById($importId);
        
        $importCategoryModel = new Application_Model_ImportCategory();
        $this->view->categories = $importCategoryModel->getImportCategories($importId);
        
        $this->_helper->layout->disableLayout();
    }

    public function billAction()
    {
        $importId = $this->getRequest()->getParam('id');
        $importModel = new Application_Model_Import();
        
        $this->view->import = $importModel->getImportById($importId);
        
        $importCategoryModel = new Application_Model_ImportCategory();
        $categories = $importCategoryModel->getImportCategories($importId);
        $this->view->categories = $categories;
        
        $totalBuyPrice = 0;
        $totalSellPrice = 0;
        foreach ($categories as $category){
            $totalBuyPrice += $category['categoryQuantity'] * $category['categoryBuyPrice'];
            $totalSellPrice += $category['categoryQuantity'] * $category['categorySellPrice'];
        }
        $this->view->totalBuyPrice = $totalBuyPrice;
        $this->view->totalSellPrice = $totalSellPrice;
        
        $this->_helper->layout->disableLayout();
    }


}


