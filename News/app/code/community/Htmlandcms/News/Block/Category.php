<?php

/**
 * Class Htmlandcms_News_Block_Category
 */
class Htmlandcms_News_Block_Category extends Mage_Core_Block_Template
{
    /**
     * Htmlandcms_News_Block_Category constructor.
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
        parent::_prepareLayout();
        return $this;
    }
}