<?php
class Vladimird_Faq_Block_Adminhtml_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml';
        $this->_blockGroup = 'vladimird_faq';
        $this->_mode = 'edit';
                
        $faq_id = (int)$this->getRequest()->getParam($this->_objectId);
        $faq = Mage::getModel('vladimird_faq/faq')->load($faq_id);
        Mage::register('current_faq', $faq);
    }
    
    protected function _prepareLayout() {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }
 
    public function getHeaderText()
    {
        $faq = Mage::registry('current_faq');
        if ($faq->getFaqId()) { 
            return Mage::helper('vladimird_faq')->__("Edit FAQ '%s'", $this->escapeHtml($faq->getTitle()));
        } else {
            return Mage::helper('vladimird_faq')->__("Add new FAQ");
        }
    }
    

       
}
