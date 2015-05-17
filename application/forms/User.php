<?php

class Application_Form_User extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        
        $this->addElement('text','userFullname', array(
            'label' => $this->getTranslator()->translate('Full Name'),
            'required' => true,
            'validators' => array(
                array('notEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => $this->getTranslator()->translate('Value is required')
                        )
                ))
            )
        ));
        
        $this->addElement('text','userName', array(
            'label' => $this->getTranslator()->translate('Username'),
            'required' => true,
            'validators' => array(
                array('notEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => $this->getTranslator()->translate('Value is required')
                        )
                ))
            )
        ));
        
        $this->addElement('password','userPassword', array(
            'label' => $this->getTranslator()->translate('Password'),
            'required' => true,
            'validators' => array(
                array('notEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => $this->getTranslator()->translate('Value is required')
                        )
                ))
            )
        ));
        
        $roleModel = new Application_Model_Role();
        $roles = array();
        foreach ($roleModel->getRoles() as $role)
            $roles[$role['roleId']] = $role['roleName'];
        
        $this->addElement('select','userRole', array(
            'label' => $this->getTranslator()->translate('Role'),
            'required' => true,
            'multiOptions' => $roles,
            'validators' => array(
                array('notEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => $this->getTranslator()->translate('Value is required')
                        )
                ))
            )
        ));
        
        $this->addElement('submit','submit',array(
            'label' => $this->getTranslator()->translate('Add User'),
            'class' => 'btn btn-success'
        ));
    }


}
