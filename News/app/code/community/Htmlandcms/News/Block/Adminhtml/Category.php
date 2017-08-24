<?php

/**
 * Class Htmlandcms_News_Block_Adminhtml_Category
 */
class Htmlandcms_News_Block_Adminhtml_Category extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    /**
     * Htmlandcms_News_Block_Adminhtml_Category constructor.
     */
    public function __construct()
    {
        $this->_controller = "adminhtml_category";
        $this->_blockGroup = "news";
        $this->_headerText = Mage::helper("news")->__("Category Manager");
        $this->_addButtonLabel = Mage::helper("news")->__("Add New Item");
        parent::__construct();

    }

}