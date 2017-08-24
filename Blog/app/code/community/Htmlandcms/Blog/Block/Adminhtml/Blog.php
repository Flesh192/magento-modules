<?php


class Htmlandcms_Blog_Block_Adminhtml_Blog extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{
        $this->_controller = 'adminhtml_blog';
        $this->_blockGroup = 'blog';
        $this->_headerText = Mage::helper('blog')->__('Blog manager');
        $this->_addButtonLabel = Mage::helper('blog')->__('Add new item');
        parent::__construct();
	
	}

}