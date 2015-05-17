<?php

class UserController extends Zend_Controller_Action
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
        $userModel = new Application_Model_User();
        
        $this->view->users = $userModel->getUsers();
    }

    public function addAction()
    {
        $userForm = new Application_Form_User();
        
        if($this->getRequest()->isPost()){
            $data = $this->getRequest()->getPost();
            unset($data['submit']);
            
            if($userForm->isValid($data)){
                $userModel = new Application_Model_User();
                $data['userPassword'] = md5($data['userPassword']);
                $userModel->addUser($data);
                $this->redirect("/user");
            } else {
                $userForm->populate($data);
            }
        }
        
        $this->view->form = $userForm;
    }

    public function editAction()
    {
        $userId = $this->getRequest()->getParam('id');
        $userModel = new Application_Model_User();
        $user = $userModel->fetchRow("userId=$userId")->toArray();
        
        $userForm = new Application_Form_User();
        
        $translate = Zend_Registry::get('Zend_Translate');
        $userForm->getElement('userPassword')
                 ->setRequired(false);
        
        $userForm->getElement('submit')
                 ->setLabel($translate->translate('Edit User'))
                 ->setAttrib('class', 'btn btn-warning');
        
        if($this->getRequest()->isPost()){
            $data = $this->getRequest()->getPost();
            unset($data['submit']);
            
            if($userForm->isValid($data)){
                $userModel = new Application_Model_User();
                if(empty($data['userPassword'])) unset($data['userPassword']);
                else $data['userPassword'] = md5($data['userPassword']);
                $userModel->editUser($userId, $data);
                $this->redirect('/user');
            } else {
                $userForm->populate($data);
            }
            
        } else {
            $userForm->populate($user);
        }
        
        $this->view->form = $userForm;
    }

    public function deleteAction()
    {
        $userId = $this->getRequest()->getParam('id');
        $userModel = new Application_Model_User();
        
        $userModel->deleteUser($userId);
        
        $this->redirect('/user');
    }


}
