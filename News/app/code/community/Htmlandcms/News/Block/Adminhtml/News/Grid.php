<?php

/**
 * Class Htmlandcms_News_Block_Adminhtml_News_Grid
 */
class Htmlandcms_News_Block_Adminhtml_News_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * Htmlandcms_News_Block_Adminhtml_News_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId("newsGrid");
        $this->setDefaultSort("news_id");
        $this->setDefaultDir("ASC");
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return mixed
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel("news/news")->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return mixed
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            "news_id", array(
                            "header" => Mage::helper("news")->__("ID"),
                            "align"  => "right",
                            "width"  => "50px",
                            "type"   => "number",
                            "index"  => "news_id",
                       )
        );

        $this->addColumn(
            "title", array(
                          "header" => Mage::helper("news")->__("Title"),
                          "index"  => "title",
                     )
        );

        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

        return parent::_prepareColumns();
    }

    /**
     * @param $row
     * @return mixed
     */
    public function getRowUrl($row)
    {
        return $this->getUrl("*/*/edit", array("id" => $row->getId()));
    }


    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('news_id');
        $this->getMassactionBlock()->setFormFieldName('news_ids');
        $this->getMassactionBlock()->setUseSelectAll(true);
        $this->getMassactionBlock()->addItem(
            'remove_news', array(
                                'label'   => Mage::helper('news')->__('Remove News'),
                                'url'     => $this->getUrl('*/adminhtml_news/massRemove'),
                                'confirm' => Mage::helper('news')->__('Are you sure?')
                           )
        );
        return $this;
    }


}