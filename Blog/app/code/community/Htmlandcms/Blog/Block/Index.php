<?php   
class Htmlandcms_Blog_Block_Index extends Mage_Core_Block_Template{
    public function __construct()
    {
        parent::__construct();
        $datasets=Mage::getModel('blog/blog')->getCollection();
        $this->setDatasets($datasets);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $head = $this->getLayout()->getBlock('head');
        $head->setTitle($this->__('Blog'));
        $pager = $this->getLayout()->createBlock('page/html_pager')->setCollection($this->getDatasets());
        $this->setChild('pager', $pager);
        $this->getDatasets()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getBlog()
    {
        if (!$this->hasData('blog')) {
            $this->setData('blog', Mage::registry('blog'));
        }
    }
}