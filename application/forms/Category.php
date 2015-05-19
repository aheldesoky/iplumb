<?php

class Application_Form_Category extends Zend_Form
{

    public function init()
    {
        $this->setMethod('post');
        $this->setAttrib('id', 'categoryForm');
        
        $this->addElement('text','categoryName', array(
            'label' => $this->getTranslator()->translate('Category Name'),
            'required' => true,
            'validators' => array(
                array('notEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => $this->getTranslator()->translate('Value is required')
                        )
                )),
                /*array('db_NoRecordExists', true, array(
                        'table' => 'category', 
                        'field' => 'categoryName', 
                        'messages' => $this->getTranslator()->translate('This category already exists')
                ))*/
            )
        ));
        /*
        $sizeModel = new Application_Model_Size();
        $sizes = array($this->getTranslator()->translate('No Size'));
        foreach ($sizeModel->getSizes() as $size)
            $sizes[$size['sizeId']] = html_entity_decode($size['sizeValue'], ENT_COMPAT, "utf-8");
        
        $this->addElement('select','categorySize', array(
            'label' => $this->getTranslator()->translate('Size'),
            'required' => true,
            'multiOptions' => $sizes,
            'validators' => array(
                array('notEmpty', true, array(
                        'messages' => array(
                            'isEmpty' => $this->getTranslator()->translate('Value is required')
                        )
                ))
            )
        ));
        */
        $this->addElement('submit','submit',array(
            'label' => $this->getTranslator()->translate('Add Category'),
            'class' => 'btn btn-success'
        ));
        
    }


}
