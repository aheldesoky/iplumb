<?php

class Application_Form_ImportCategory extends Zend_Form
{

    public function init()
    {
        
        $this->addElement('text','importCategoryId', array(
            'label' => $this->getTranslator()->translate('Category Name'),
            'required' => true,
            'validators' => array(
                array('notEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => $this->getTranslator()->translate('Value is required')
                        )
                ))
            )
        ));
        
        $this->addElement('text','importCategoryBuyPrice', array(
            'label' => $this->getTranslator()->translate('Buy Price'),
            'required' => true,
            'validators' => array(
                array('notEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => $this->getTranslator()->translate('Value is required')
                        )
                )),
                array('Digits', false, array(
                        'messages' => array(
                            'notDigits' => $this->getTranslator()->translate('Digits only allowed'),
                            'digitsStringEmpty' => $this->getTranslator()->translate('Digits only allowed')
                )))
            ),
        ));
        
        $this->addElement('text','importCategorySellPrice', array(
            'label' => $this->getTranslator()->translate('Sell Price'),
            'required' => true,
            'validators' => array(
                array('notEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => $this->getTranslator()->translate('Value is required')
                        )
                )),
                array('Digits', false, array(
                        'messages' => array(
                            'notDigits' => $this->getTranslator()->translate('Digits only allowed'),
                            'digitsStringEmpty' => $this->getTranslator()->translate('Digits only allowed')
                )))
            ),
        ));
        
    }

}

