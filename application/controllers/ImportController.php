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
        $page = $this->getRequest()->getParam('page');
        $importModel = new Application_Model_Import();
        
        $this->view->imports = $importModel->getImports();
    }

    public function addAction()
    {
        $importForm = new Application_Form_Import();
        
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
        $import = $importModel->fetchRow("importId=$importId")->toArray();
        
        $importForm = new Application_Form_Import();
        
        $translate = Zend_Registry::get('Zend_Translate');
        $importForm->getElement('submit')
                     ->setLabel($translate->translate('Edit Import'))
                     ->setAttrib('class', 'btn btn-warning');
        
        if($this->getRequest()->isPost()){
            $data = $this->getRequest()->getPost();
            echo '<pre>';print_r($data);
            unset($data['submit']);
            unset($data['importCategories']);
            $importForm->removeElement('importCategories');
            if($importForm->isValid($data)){
                $importModel = new Application_Model_Import();
                $importModel->editImport($importId, $data);
                $this->redirect('/import');
            } else {
                $importForm->populate($data);
            }
            
        } else {
            $importForm->populate($import);
        }
        
        $this->view->form = $importForm;
    }

    public function deleteAction()
    {
        $importId = $this->getRequest()->getParam('id');
        $importModel = new Application_Model_Import();
        
        $importModel->deleteImport($importId);
        
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
    }


}
