<?php
class Vladimird_Faq_Block_Adminhtml_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $faq = Mage::registry('current_faq');
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('edit_faq', array(
                'legend' => Mage::helper('vladimird_faq')->__('FAQ details')
        ));

        if ($faq->getFaq_id()) {
            $fieldset->addField('faq_id', 'hidden', array(
                    'name'      => 'faq_id',
                    'required'  => true,
            ));
        }
 
        $fieldset->addField('title', 'text', array(
                'name'      => 'title',
                'label'     => Mage::helper('vladimird_faq')->__('Title'),
                'maxlength' => '150',
                'required'  => true,
        ));
        
        /*WYSIWYG block*/
        $fieldset->addField('text', 'editor', array(
            'name'      => 'text',
            'label'     => Mage::helper('vladimird_faq')->__('Content'),
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            'wysiwyg'   => true,
            'required'  => true,
            ));
 
        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('*/*/save'));
        $form->setValues($faq->getData());
 
        $this->setForm($form);
        
    }    
} 
