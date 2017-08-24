<?php
class Vladimird_Feedback_Block_Adminhtml_Feedback extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_feedback';
        $this->_blockGroup = 'vladimird_feedback';
        $this->_headerText = Mage::helper('vladimird_feedback')->__('Feedback Manager');        
    }
}
