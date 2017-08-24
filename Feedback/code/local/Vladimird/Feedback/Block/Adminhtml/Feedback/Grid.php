<?php
class Vladimird_Feedback_Block_Adminhtml_Feedback_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        //die ('grid');
        parent::__construct();
        $this->setId('feedbackGrid');
        $this->setDefaultSort('feedback_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('vladimird_feedback/feedback')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('feedback_id', array('header' => Mage::helper('vladimird_feedback')->__('ID'), 
                         'align' => 'right', 
                         'width' => '50px', 
                         'index' => 'feedback_id',
                         ));
        $this->addColumn('comment', array('header' => Mage::helper('vladimird_feedback')->__('Title'), 
                                        'align' => 'left', 
                                        'index' => 'comment',));
        $this->addColumn('reason', array('header' => Mage::helper('vladimird_feedback')->__('Reason'),
                                        'align' => 'left',
                                        'index' => 'reason',
                                        ));
        $this->addColumn('email', array('header' => Mage::helper('vladimird_feedback')->__('e-mail'),
                                        'align' => 'left',
                                        'index' => 'email',
                                        ));
        $this->addColumn('action', array('header' => Mage::helper('vladimird_feedback')->__('Action'),
                                        'width' => '100',
                                        'type' => 'action',
                                        'getter' => 'getId',
                                        'actions' => array(
                                                          array(
                                                                'caption' => Mage::helper('vladimird_feedback')->__('Delete'),
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
        $this->setMassactionIdField('feedback_id');
        $this->getMassactionBlock()->setFormFieldName('feedback');
        $this->getMassactionBlock()->addItem('delete', array( 'label' => Mage::helper('vladimird_feedback')->__('Delete'), 'url' => $this->getUrl('*/*/massDelete'), 'confirm' => Mage::helper('vladimird_feedback')->__('Are you sure?')));
        return $this;
    }
    
} 
