<?php
class Vladimird_Faq_Block_Adminhtml_Faq_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function _construct()
    {
        $this->setId('faqGrid');
        $this->_controller = 'adminhtml_faq';
        $this->setUseAjax(true);
        $this->setDefaultSort('faq_id');
        $this->setDefaultDir('ASC');
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('vladimird_faq/faq')->getCollection();
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('faq_id', array('header' => Mage::helper('vladimird_faq')->__('ID'), 
                         'align' => 'right', 
                         'width' => '20px', 
                         'index' => 'faq_id',
                         ));
                         
        $this->addColumn('title', array('header' => Mage::helper('vladimird_faq')->__('Title'), 
                                        'align' => 'left', 
                                        'index' => 'title',));
                                        
        $this->addColumn('edit', array('header' => Mage::helper('vladimird_feedback')->__('Action'),
                                        'width' => '100',
                                        'align' => 'right',
                                        'type' => 'action',
                                        'getter' => 'getId',
                                        'actions' => array(
                                                          array(
                                                                'caption' => Mage::helper('vladimird_faq')->__('Edit'),
                                                                'url' => array('base' => '*/*/edit'),
                                                                'field' => 'id'
                                                                )
                                                        ),
                                        'filter' => false,
                                        'sortable' => false,
                                        'index' => 'stores',
                                        'is_system' => true,
                                        ));
                                        
        $this->addColumn('delete', array('header' => Mage::helper('vladimird_faq')->__('Action'),
                                        'width' => '100',
                                        'align' => 'right',
                                        'type' => 'action',
                                        'getter' => 'getId',
                                        'actions' => array(
                                                          array(
                                                                'caption' => Mage::helper('vladimird_faq')->__('Delete'),
                                                                'url' => array('base' => '*/*/delete'),
                                                                'field' => 'id'
                                                                )
                                                        ),
                                        'filter' => false,
                                        'sortable' => false,
                                        'index' => 'stores',
                                        'is_system' => true,
                                        ));
                                        
        return parent::_prepareColumns();
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('faq_id');
        $this->getMassactionBlock()->setFormFieldName('faq');
        $this->getMassactionBlock()->addItem('delete', array( 'label' => Mage::helper('vladimird_faq')->__('Delete'), 'url' => $this->getUrl('*/*/massDelete'), 'confirm' => Mage::helper('vladimird_faq')->__('Are you sure?')));
        return $this;
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
} 
