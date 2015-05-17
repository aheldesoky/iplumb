<?php

class RoleController extends Zend_Controller_Action
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
        $roleModel = new Application_Model_Role();
        
        $this->view->roles = $roleModel->getRoles();
    }

    public function addAction()
    {
        $roleForm = new Application_Form_Role();
        
        if($this->getRequest()->isPost()){
            $data = $this->getRequest()->getPost();
            unset($data['submit']);
            
            if($roleForm->isValid($data)){
                $roleModel = new Application_Model_Role();
                $roleModel->addRole($data);
                $this->redirect("/role");
            } else {
                $roleForm->populate($data);
            }
        }
        
        $this->view->form = $roleForm;
    }

    public function editAction()
    {
        $roleId = $this->getRequest()->getParam('id');
        $roleModel = new Application_Model_Role();
        $role = $roleModel->fetchRow("roleId=$roleId")->toArray();
        
        $roleForm = new Application_Form_Role();
        
        $translate = Zend_Registry::get('Zend_Translate');
        $roleForm->getElement('submit')
                 ->setLabel($translate->translate('Edit Role'))
                 ->setAttrib('class', 'btn btn-warning');
        
        if($this->getRequest()->isPost()){
            $data = $this->getRequest()->getPost();
            unset($data['submit']);
            
            if($data['roleName'] == $size['roleName'])
                $sizeForm->getElement ('roleName')->removeValidator ('db_NoRecordExists');
            
            if($roleForm->isValid($data)){
                $roleModel = new Application_Model_Role();
                $roleModel->editRole($roleId, $data);
                $this->redirect('/role');
            } else {
                $roleForm->populate($data);
            }
            
        } else {
            $roleForm->populate($role);
        }
        
        $this->view->form = $roleForm;
    }

    public function deleteAction()
    {
        $roleId = $this->getRequest()->getParam('id');
        $roleModel = new Application_Model_Role();
        
        $roleModel->deleteRole($roleId);
        
        $this->redirect('/role');
    }


}
