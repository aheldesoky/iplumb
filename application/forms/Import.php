<?php

class Application_Form_Import extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'importForm');
        
        $this->addElement('hidden','importCategories', array(
            'required' => true,
        ));
        
        $supplierModel = new Application_Model_Supplier();
        $suppliers = array('' => $this->getTranslator()->translate('Select Supplier'));
        foreach ($supplierModel->getSuppliers() as $supplier)
            $suppliers[$supplier['supplierId']] = $supplier['supplierName'];
        
        $this->addElement('select','importSupplier', array(
            'label' => $this->getTranslator()->translate('Supplier'),
            'required' => true,
            'multiOptions' => $suppliers,
            'validators' => array(
                array('notEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => $this->getTranslator()->translate('Value is required')
                        )
                ))
            )
        ));
        
        $this->addElement('text','importOrder', array(
            'label' => $this->getTranslator()->translate('Order ID'),
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
        
        $this->addElement('text', 'importDate', array(
            'label' => $this->getTranslator()->translate('Date'),
            'value' => date('Y-m-d'),
            'required' => true,
        ));
        /*
        $this->addElement('button','addcategory',array(
            'label' => $this->getTranslator()->translate('Add Category to Import'),
            'class' => 'btn btn-info'
        ));
        */
        $this->addElement('submit','submit',array(
            'label' => $this->getTranslator()->translate('Add Import'),
            'class' => 'btn btn-success'
        ));
    }


}

