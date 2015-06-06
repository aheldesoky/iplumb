<?php

class Application_Form_Sale extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'saleForm');
        
        $this->addElement('hidden','saleCategories', array(
            'required' => true,
        ));
        
        $this->addElement('text','saleDiscount', array(
            'label' => $this->getTranslator()->translate('Sale Discount'),
            //'required' => true,
            'class' => 'number form-control',
            //'disabled' => true,
            'validators' => array(
                array('notEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => $this->getTranslator()->translate('You must enter discount percentage')
                        )
                )),
                array('Digits', false, array(
                        'messages' => array(
                            'notDigits' => $this->getTranslator()->translate('Digits only allowed'),
                            'digitsStringEmpty' => $this->getTranslator()->translate('Digits only allowed')
                )))
            ),
            'decorators' => array(
                array('ViewHelper'),
                array('Label',       array('tag' => 'div', 'class' => 'form-label')),
                array('HtmlTag', array('tag' => 'div')),
                array('Description'),
                array('Errors', array('class'=>'error')),
                array('HtmlTag', array('tag' => 'div', 'class'=>'col-md-6 form-group')),
            )
        ));
        
        $this->addElement('text','customerName', array(
            'label' => $this->getTranslator()->translate('Customer Name'),
            'class' => 'form-control',
            //'required' => true,
            //'disabled' => true,
            'validators' => array(
                array('notEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => $this->getTranslator()->translate('You must enter customer name')
                        )
                ))
            ),
            'decorators' => array(
                array('ViewHelper'),
                array('Label',       array('tag' => 'div', 'class' => 'form-label', 'requiredSuffix' => ' *')),
                array('HtmlTag', array('tag' => 'div')),
                array('Description'),
                array('Errors', array('class'=>'error')),
                array('HtmlTag', array('tag' => 'div', 'class'=>'col-md-6 form-group')),
            )
        ));
        
        $this->addElement('text', 'saleDate', array(
            'label' => $this->getTranslator()->translate('Date'),
            'class' => 'form-control',
            //'value' => date('Y-m-d'),
            'data-date-format'=>'YYYY-MM-DD',
            'required' => true,
            'validators' => array(
                array('notEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => $this->getTranslator()->translate('Date is required')
                        )
                )),
                //array('date', true, array('Y-M-D'))
            ),
            'decorators' => array(
                array('ViewHelper'),
                array('Label',       array('tag' => 'div', 'class' => 'form-label', 'requiredSuffix' => ' *')),
                array('HtmlTag', array('tag' => 'div')),
                array('Description'),
                array('Errors', array('class'=>'error')),
                array('HtmlTag', array('tag' => 'div', 'class'=>'col-md-6 form-group')),
            )
        ));
        
        $this->addElement('text','customerPhone', array(
            'label' => $this->getTranslator()->translate('Customer Phone'),
            'class' => 'form-control',
            //'required' => true,
            //'disabled' => true,
            'validators' => array(
                array('notEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => $this->getTranslator()->translate('You must enter customer phone')
                        )
                ))
            ),
            'decorators' => array(
                array('ViewHelper'),
                array('Label',       array('tag' => 'div', 'class' => 'form-label', 'requiredSuffix' => ' *')),
                array('HtmlTag', array('tag' => 'div')),
                array('Description'),
                array('Errors', array('class'=>'error')),
                array('HtmlTag', array('tag' => 'div', 'class'=>'col-md-6 form-group')),
            )
        ));
        
        $this->addElement('textarea','customerNotes', array(
            'label' => $this->getTranslator()->translate('Customer Notes'),
            'class' => 'form-control',
            'cols'  => 40,
            'rows'  => 4,
            //'required' => true,
            //'disabled' => true,
            'validators' => array(
                array('notEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => $this->getTranslator()->translate('You must enter customer notes')
                        )
                ))
            ),
            'decorators' => array(
                array('ViewHelper'),
                array('Label',       array('tag' => 'div', 'class' => 'form-label', 'requiredSuffix' => ' *')),
                array('HtmlTag', array('tag' => 'div')),
                array('Description'),
                array('Errors', array('class'=>'error')),
                array('HtmlTag', array('tag' => 'div', 'class'=>'col-md-6 col-md-offset-6 form-group')),
            )
        ));
        
        $this->addElement('submit','submit',array(
            'label' => $this->getTranslator()->translate('Checkout Bill'),
            'class' => 'btn btn-danger btn-lg',
            'decorators' => array(
                array('ViewHelper'),
                array('HtmlTag', array('tag' => 'div')),
                array('Description'),
                array('Errors', array('class'=>'error')),
                array('HtmlTag', array('tag' => 'div', 'class'=>'col-md-12 form-group text-center')),
            )
        ));
    }


}

