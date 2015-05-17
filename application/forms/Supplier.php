<?php

class Application_Form_Supplier extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        
        $this->addElement('text','supplierName', array(
            'label' => $this->getTranslator()->translate('Supplier Name'),
            'required' => true,
            'validators' => array(
                array('notEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => $this->getTranslator()->translate('Value is required')
                        )
                ))
            )
        ));
        
        $this->addElement('text','supplierPhone', array(
            'label' => $this->getTranslator()->translate('Phone Number'),
            'required' => false,
            'validators' => array(
                array('Digits', false, array(
                        'messages' => array(
                            'notDigits' => $this->getTranslator()->translate('Digits only allowed'),
                            'digitsStringEmpty' => $this->getTranslator()->translate('Digits only allowed')
                )))
            )
        ));
        
        $this->addElement('textarea','supplierAddress', array(
            'label' => $this->getTranslator()->translate('Address'),
            'required' => false,
            'cols' => 25,
            'rows' => 4,
        ));
        /*
        $this->addElement('text','supplierBalance', array(
            'label' => $this->getTranslator()->translate('Initial Balance'),
            'required' => false,
            'validators' => array(
                array('Digits', false, array(
                        'messages' => array(
                            'notDigits' => $this->getTranslator()->translate('Digits only allowed'),
                            'digitsStringEmpty' => $this->getTranslator()->translate('Digits only allowed')
                )))
            ),
        ));
        */
        $this->addElement('submit','submit',array(
            'label' => $this->getTranslator()->translate('Add Supplier'),
            'class' => 'btn btn-success'
        ));
    }


}

