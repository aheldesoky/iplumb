<?php

class Application_Form_Import extends Zend_Form
{

    protected $_importId;
    
    public function setImportId($importId = null)
    {
        $this->_importId = $importId;
        return $this;
    }

    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'importForm');
        /*
        if($this->_importId){
            $importCategoryModel = new Application_Model_ImportCategory();
            $categories = $importCategoryModel->getImportCategories($this->_importId);
            foreach ($categories as $category){
                $currentCategory['categoryId'] = $category['categoryId'];
                $currentCategory['categoryQuantity'] = $category['categoryQuantity'];
                $currentCategory['categoryBuyPrice'] = $category['categoryBuyPrice'];
                $currentCategory['categorySellPrice'] = $category['categorySellPrice'];
                $importCategories[] = $currentCategory;
            }
            $importCategoriesJSON = json_encode($importCategories);
        }
        */
        $this->addElement('hidden','importCategories', array(
            'required' => true,
        ));
        
        $this->addElement('text','importDiscount', array(
            'label' => $this->getTranslator()->translate('Import Discount Percentage'),
            'required' => true,
            'class' => 'number',
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
        ));
        
        $supplierModel = new Application_Model_Supplier();
        $suppliers = array('0' => $this->getTranslator()->translate('No Supplier'));
        $suppliersArr = $supplierModel->getSuppliers();
        if(!empty($suppliersArr))
            foreach ($suppliersArr as $supplier)
                $suppliers[$supplier['supplierId']] = $supplier['supplierName'];
        
        $this->addElement('select','importSupplier', array(
            'label' => $this->getTranslator()->translate('Supplier'),
            'required' => true,
            'multiOptions' => $suppliers,
            'validators' => array(
                array('notEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => $this->getTranslator()->translate('You must select supplier')
                        )
                ))
            )
        ));
        
        $this->addElement('text','importOrder', array(
            'label' => $this->getTranslator()->translate('Order ID'),
            //'required' => true,
            //'class' => 'number',
            'validators' => array(
                array('notEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => $this->getTranslator()->translate('Value is required')
                        )
                )),
                /*array('Digits', false, array(
                        'messages' => array(
                            'notDigits' => $this->getTranslator()->translate('Digits only allowed'),
                            'digitsStringEmpty' => $this->getTranslator()->translate('Digits only allowed')
                )))*/
            ),
        ));
        
        $this->addElement('text', 'importDate', array(
            'label' => $this->getTranslator()->translate('Date'),
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
            )
        ));
        /*
        $this->addElement('button','addcategory',array(
            'label' => $this->getTranslator()->translate('Add Category to Import'),
            'class' => 'btn btn-info'
        ));
        */
        $this->addElement('submit','submit',array(
            'label' => $this->getTranslator()->translate('Add Import'),
            'class' => 'btn btn-success btn-lg',
            /*'decorators' => array(
                    array(
                        'HtmlTag', array(
                            'tag'  => 'dl',
                            'class' => 'text-center'
                        )
                    )
                )*/
        ));
    }


}

