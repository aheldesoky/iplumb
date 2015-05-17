<?php

class AuthController extends Zend_Controller_Action
{

    public function init()
    {
        
    }

    public function indexAction()
    {
        // action body
    }

    public function loginAction()
    {
        //checking if user is authenticated or not
        $authorization = Zend_Auth::getInstance();
        if($authorization->hasIdentity()) {
            $this->redirect('/');
        }
        
        // Disable layout
        //$this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        
        if($this->getRequest()->isPost()){
            $username = $this->_request->getParam('username');
            $password = $this->_request->getParam('password');

            // get the default db adapter
            $db = Zend_Db_Table::getDefaultAdapter();

            //create the auth adapter
            $authAdapter = new Zend_Auth_Adapter_DbTable($db,'user','userName','userPassword');

            //set the email and password
            $authAdapter->setIdentity($username);
            $authAdapter->setCredential(md5($password));

            //authenticate
            $result = $authAdapter->authenticate();
            if ($result->isValid()) {
                //if the user is valid register his info in session
                $auth = Zend_Auth::getInstance();
                $storage = $auth->getStorage();
                $storage->write($authAdapter->getResultRowObject(array('userId','userName','userFullname','userRole')));
                $this->redirect('/');
            }else{
                echo 'failed';
            }
        }
        
    }

    public function logoutAction()
    {
        //Zend_Session::destroy();
        Zend_Session::namespaceUnset('Zend_Auth');
        $this->redirect('/auth/login');
    }

    public function registerAction()
    {
        // action body
    }


}

