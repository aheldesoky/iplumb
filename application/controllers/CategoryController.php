<?php

class CategoryController extends Zend_Controller_Action
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
        $categoryModel = new Application_Model_Category();
        
        $this->view->categorys = $categoryModel->getCategorys();
    }

    public function addAction()
    {
        $categoryForm = new Application_Form_Category();
        
        if($this->getRequest()->isPost()){
            $data = $this->getRequest()->getPost();
            unset($data['submit']);
            
            if($categoryForm->isValid($data)){
                $categoryModel = new Application_Model_Category();
                $categoryModel->addCategory($data);
                $this->redirect("/category");
            } else {
                $categoryForm->populate($data);
            }
        }
        
        $this->view->form = $categoryForm;
    }

    public function editAction()
    {
        $categoryId = $this->getRequest()->getParam('id');
        $categoryModel = new Application_Model_Category();
        $category = $categoryModel->fetchRow("categoryId=$categoryId")->toArray();
        
        $categoryForm = new Application_Form_Category();
        
        $translate = Zend_Registry::get('Zend_Translate');
        $categoryForm->getElement('submit')
                     ->setLabel($translate->translate('Edit Category'))
                     ->setAttrib('class', 'btn btn-warning');
        
        if($this->getRequest()->isPost()){
            $data = $this->getRequest()->getPost();
            unset($data['submit']);
            
            if($data['categoryName'] == $size['categoryName'])
                $sizeForm->getElement ('categoryName')->removeValidator ('db_NoRecordExists');
            
            if($categoryForm->isValid($data)){
                $categoryModel = new Application_Model_Category();
                $categoryModel->editCategory($categoryId, $data);
                $this->redirect('/category');
            } else {
                $categoryForm->populate($data);
            }
            
        } else {
            $categoryForm->populate($category);
        }
        
        $this->view->form = $categoryForm;
    }

    public function deleteAction()
    {
        $categoryId = $this->getRequest()->getParam('id');
        $categoryModel = new Application_Model_Category();
        
        $categoryModel->deleteCategory($categoryId);
        
        $this->redirect('/category');
    }

    public function queryAction()
    {
        $query = $this->getRequest()->getParam('query');
        $import = $this->getRequest()->getParam('import');
        $categoryModel = new Application_Model_Category();
        
        $result = $categoryModel->getCategorysByQuery($query, $import);
        
        $this->getHelper('json')->sendJson(array(
            'query' => $query,
            'suggestions' => $result
        ));
    }

    public function ajaxAction()
    {
        $categoryForm = new Application_Form_Category();
        
        if($this->getRequest()->isPost()){
            $data = $this->getRequest()->getPost();
            unset($data['submit']);
            
            if($categoryForm->isValid($data)){
                $categoryModel = new Application_Model_Category();
                $categoryModel->addCategory($data);
                $translate = Zend_Registry::get('Zend_Translate');
                $this->getHelper('json')->sendJson( array('success' => $translate->translate('Category has been added successfully')) );
            } else {
                $this->getHelper('json')->sendJson( array('errors' => $categoryForm->getMessages()) );
            }
        }
        
    }

}
