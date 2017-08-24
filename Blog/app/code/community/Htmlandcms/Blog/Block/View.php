<?php   
class Htmlandcms_Blog_Block_View extends Mage_Core_Block_Template{
    public function __construct()
    {
        parent::__construct();
        $datasets=Mage::getModel('blog/blog')->getCollection();
        $this->setDatasets($datasets);
    }

    protected function _prepareLayout()
    {
        $head = $this->getLayout()->getBlock('head');
        $data = Mage::getModel('blog/blog')->load($this->getRequest()->getParam('id'));
        $head->setTitle($data->getData('meta_title'));
        $head->setDescription($data->getData('description'));
        $head->setKeywords($data->getData('keywords'));
        parent::_prepareLayout();
        return $this;
    }
}