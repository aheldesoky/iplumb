<?php

class Application_Form_Size extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        
        $this->addElement('text','sizeValue', array(
            'label' => $this->getTranslator()->translate('Size Value'),
            'required' => true,
            'validators' => array(
                array('notEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => $this->getTranslator()->translate('Value is required')
                        )
                )),
                array('db_NoRecordExists', true, array(
                        'table' => 'size', 
                        'field' => 'sizeValue', 
                        'messages' => $this->getTranslator()->translate('This size already exists')
                ))
            )
        ));
        
        $this->addElement('submit','submit',array(
            'label' => $this->getTranslator()->translate('Add Size'),
            'class' => 'btn btn-success'
        ));
        
    }


}

