<?php

class SupplierController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $page = $this->getRequest()->getParam('page');
        $supplierModel = new Application_Model_Supplier();
        
        $this->view->suppliers = $supplierModel->getSuppliers();
    }

    public function addAction()
    {
        $supplierForm = new Application_Form_Supplier();
        
        if($this->getRequest()->isPost()){
            $data = $this->getRequest()->getPost();
            unset($data['submit']);
            
            if($supplierForm->isValid($data)){
                $supplierModel = new Application_Model_Supplier();
                $supplierModel->addSupplier($data);
                $this->redirect("/supplier");
            } else {
                $supplierForm->populate($data);
            }
        }
        
        $this->view->form = $supplierForm;
    }

    public function editAction()
    {
        $supplierId = $this->getRequest()->getParam('id');
        $supplierModel = new Application_Model_Supplier();
        $supplier = $supplierModel->fetchRow("supplierId=$supplierId")->toArray();
        
        $supplierForm = new Application_Form_Supplier();
        
        $translate = Zend_Registry::get('Zend_Translate');
        $supplierForm->getElement('submit')
                     ->setLabel($translate->translate('Edit Supplier'))
                     ->setAttrib('class', 'btn btn-warning');
        
        if($this->getRequest()->isPost()){
            $data = $this->getRequest()->getPost();
            unset($data['submit']);
            
            if($supplierForm->isValid($data)){
                $supplierModel = new Application_Model_Supplier();
                $supplierModel->editSupplier($supplierId, $data);
                $this->redirect('/supplier');
            } else {
                $supplierForm->populate($data);
            }
            
        } else {
            $supplierForm->populate($supplier);
        }
        
        $this->view->form = $supplierForm;
    }

    public function deleteAction()
    {
        $supplierId = $this->getRequest()->getParam('id');
        $supplierModel = new Application_Model_Supplier();
        
        $supplierModel->deleteSupplier($supplierId);
        
        $this->redirect('/supplier');
    }


}
