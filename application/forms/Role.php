<?php

class Application_Form_Role extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        
        $this->addElement('text','roleName', array(
            'label' => $this->getTranslator()->translate('Role Name'),
            'required' => true,
            'validators' => array(
                array('notEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => $this->getTranslator()->translate('Value is required')
                        )
                )),
                array('db_NoRecordExists', true, array(
                        'table' => 'role', 
                        'field' => 'roleName', 
                        'messages' => $this->getTranslator()->translate('This role already exists')
                ))
            )
        ));
        
        $this->addElement('submit','submit',array(
            'label' => $this->getTranslator()->translate('Add Role'),
            'class' => 'btn btn-success'
        ));
        
    }


}
