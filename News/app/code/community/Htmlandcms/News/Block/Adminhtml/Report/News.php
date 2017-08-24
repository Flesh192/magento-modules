<?php

/**
 * Class Htmlandcms_News_Block_Adminhtml_Report_News
 */
class Htmlandcms_News_Block_Adminhtml_Report_News extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Htmlandcms_News_Block_Adminhtml_Report_News constructor.
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_report_news';
        $this->_blockGroup = "news";
        $this->_headerText = Mage::helper('news')->__('News Report');
        parent::__construct();
        $this->_removeButton('add');
    }
}