<?php   
class Htmlandcms_Blog_Block_Category extends Mage_Core_Block_Template{
    public function __construct()
    {
        parent::__construct();
        $datasets=Mage::getModel('blog/blog')->getCollection();
        $this->setDatasets($datasets);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        return $this;
    }
}