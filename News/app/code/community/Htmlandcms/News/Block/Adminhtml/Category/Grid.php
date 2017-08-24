<?php

/**
 * Class Htmlandcms_News_Block_Adminhtml_Category_Grid
 */
class Htmlandcms_News_Block_Adminhtml_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * Htmlandcms_News_Block_Adminhtml_Category_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId("newsGrid");
        $this->setDefaultSort("cat_id");
        $this->setDefaultDir("ASC");
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return mixed
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel("news/category")->getCollection();
        foreach ($collection as $link) {
            if ($link->getStoreId() && $link->getStoreId() != 0) {
                $link->setStoreId(explode(',', $link->getStoreId()));
            } else {
                $link->setStoreId(array('0'));
            }
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return mixed
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            "cat_id", array(
                           "header" => Mage::helper("news")->__("ID"),
                           "align"  => "right",
                           "width"  => "50px",
                           "type"   => "number",
                           "index"  => "cat_id",
                      )
        );

        $this->addColumn(
            "name", array(
                         "header" => Mage::helper("news")->__("Name"),
                         "index"  => "name",
                    )
        );

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn(
                'store_id', array(
                                 'header'                    => Mage::helper('news')->__('Store View'),
                                 'index'                     => 'store_id',
                                 'type'                      => 'store',
                                 'store_all'                 => true,
                                 'store_view'                => true,
                                 'sortable'                  => true,
                                 'filter_condition_callback' => array($this,
                                                                      '_filterStoreCondition'),
                            )
            );
        }

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
        $this->setMassactionIdField('cat_id');
        $this->getMassactionBlock()->setFormFieldName('cat_ids');
        $this->getMassactionBlock()->setUseSelectAll(true);
        $this->getMassactionBlock()->addItem(
            'remove_cat', array(
                               'label'   => Mage::helper('news')->__('Remove Category'),
                               'url'     => $this->getUrl('*/adminhtml_news_category/massRemove'),
                               'confirm' => Mage::helper('news')->__('Are you sure?')
                          )
        );
        return $this;
    }

    /**
     * @param $collection
     * @param $column
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $this->getCollection()->addStoreFilter($value);
    }
}