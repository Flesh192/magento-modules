<?php

/**
 * Class Htmlandcms_News_Block_Adminhtml_Report_News_Grid
 */
class Htmlandcms_News_Block_Adminhtml_Report_News_Grid extends Mage_Adminhtml_Block_Report_Grid
{

    /**
     * Htmlandcms_News_Block_Adminhtml_Report_News_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('gridNewsReport');
        $this->setTemplate('report/grid.phtml');
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('news/news_collection');
    }

    /**
     * @return mixed
     */
    protected function _prepareColumns()
    {


        $this->addColumn(
            'image', array(
                          'header'   => $this->__('image'),
                          'sortable' => false,
                          'index'    => 'image'
                     )
        );
        $this->addColumn(
            'body', array(
                         'header'   => $this->__('body'),
                         'sortable' => false,
                         'index'    => 'body'
                    )
        );
        $this->addColumn(
            'news_id', array(
                            'header'   => $this->__('news_id'),
                            'sortable' => false,
                            'index'    => 'news_id'
                       )
        );

        /*

        //demo code

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'filter_index'=>'main_table.store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
            ));
        }

        $groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

        $this->addColumn('group_id', array(
            'header'=> Mage::helper('sales')->__('Customer Group'),
            'width' => '80px',
            'index' => 'group_id',
            'filter_index'=>'customer.group_id',
            'type'      => 'options',
            'options'   => $groups,
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'filter_index'=>'main_table.created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));


        //Purchase Order #

        $this->addColumn('po_number', array(
            'header'=> Mage::helper('sales')->__('Purchase Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'po_number',
            'filter_index'=>'payment.po_number',

        ));

          $baseCurrencyCode = $this->getCurrentCurrencyCode();

          $this->addColumn('orders_sum_amount', array(
            'header'    => $this->__('Total Order Amount'),
            'width'     => '200px',
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'orders_sum_amount',
            'total'     => 'sum',
            'renderer'  => 'adminhtml/report_grid_column_renderer_currency',
        ));

        //demo code

        */

        $this->addExportType('*/*/exportNewsCsv', Mage::helper('news')->__('CSV'));
        $this->addExportType('*/*/exportNewsExcel', Mage::helper('news')->__('Excel'));

        return parent::_prepareColumns();
    }

}