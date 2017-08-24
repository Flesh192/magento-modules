<?php

/**
 * Class Htmlandcms_News_Block_Index
 */
class Htmlandcms_News_Block_Index extends Mage_Core_Block_Template
{
    /**
     * Htmlandcms_News_Block_Index constructor.
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
        $head = $this->getLayout()->getBlock('head');
        $head->setTitle($this->__('News'));
        $pager = $this->getLayout()->createBlock('page/html_pager')->setCollection($this->getDatasets());
        $this->setChild('pager', $pager);
        $this->getDatasets()->load();
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Set data to block
     */
    public function getNews()
    {
        if (!$this->hasData('news')) {
            $this->setData('news', Mage::registry('news'));
        }
    }
}