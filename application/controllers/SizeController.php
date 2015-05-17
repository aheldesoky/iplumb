<?php

class SizeController extends Zend_Controller_Action
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
        $sizeModel = new Application_Model_Size();
        
        $this->view->sizes = $sizeModel->getSizes();
    }
    
    public function addAction()
    {
        $sizeForm = new Application_Form_Size();
        
        if($this->getRequest()->isPost()){
            $data = $this->getRequest()->getPost();
            unset($data['submit']);
            
            if($sizeForm->isValid($data)){
                $sizeModel = new Application_Model_Size();
                $sizeModel->addSize($data);
                $this->redirect("/size");
            } else {
                $sizeForm->populate($data);
            }
        }
        
        $this->view->form = $sizeForm;
    }

    public function editAction()
    {
        $sizeId = $this->getRequest()->getParam('id');
        $sizeModel = new Application_Model_Size();
        $size = $sizeModel->fetchRow("sizeId=$sizeId")->toArray();
        
        $sizeForm = new Application_Form_Size();
        
        $translate = Zend_Registry::get('Zend_Translate');
        $sizeForm->getElement('submit')
                     ->setLabel($translate->translate('Edit Size'))
                     ->setAttrib('class', 'btn btn-warning');
        
        if($this->getRequest()->isPost()){
            $data = $this->getRequest()->getPost();
            unset($data['submit']);
            
            if($data['sizeValue'] == $size['sizeValue'])
                $sizeForm->getElement ('sizeValue')->removeValidator ('db_NoRecordExists');
            
            if($sizeForm->isValid($data)){
                $sizeModel = new Application_Model_Size();
                $sizeModel->editSize($sizeId, $data);
                $this->redirect('/size');
            } else {
                $sizeForm->populate($data);
            }
            
        } else {
            $sizeForm->populate($size);
        }
        
        $this->view->form = $sizeForm;
    }

    public function deleteAction()
    {
        $sizeId = $this->getRequest()->getParam('id');
        $sizeModel = new Application_Model_Size();
        
        $sizeModel->deleteSize($sizeId);
        
        $this->redirect('/size');
    }

}







