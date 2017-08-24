<?php
class Vladimird_Faq_Block_Adminhtml_Faq extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_faq';
        $this->_blockGroup = 'vladimird_faq';
        $this->_addButtonLabel = Mage::helper('vladimird_faq')->__('Add new FAQ');
        $this->_headerText = Mage::helper('vladimird_faq')->__("Faq's");
        parent::__construct();
    }
}
