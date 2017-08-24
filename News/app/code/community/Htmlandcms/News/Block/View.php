<?php

/**
 * Class Htmlandcms_News_Block_View
 */
class Htmlandcms_News_Block_View extends Mage_Core_Block_Template
{
    /**
     * Htmlandcms_News_Block_View constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $datasets = Mage::getModel('news/news')->getCollection();
        $this->setDatasets($datasets);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $head = $this->getLayout()->getBlock('head');
        $data = Mage::getModel('news/news')->load($this->getRequest()->getParam('id'));
        $head->setTitle($data->getData('meta_title'));
        $head->setDescription($data->getData('description'));
        $head->setKeywords($data->getData('keywords'));
        parent::_prepareLayout();
        return $this;
    }
}