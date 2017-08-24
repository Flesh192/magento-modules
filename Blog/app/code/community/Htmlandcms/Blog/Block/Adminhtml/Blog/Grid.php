<?php

class Htmlandcms_Blog_Block_Adminhtml_Blog_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('blogGrid');
        $this->setDefaultSort('blog_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

		protected function _prepareCollection()
		{
				$collection = Mage::getModel('blog/blog')->getCollection();
				$this->setCollection($collection);
				return parent::_prepareCollection();
		}
		protected function _prepareColumns()
		{
				$this->addColumn('blog_id', array(
                    'header' => Mage::helper('blog')->__('ID'),
                    'align' =>'right',
                    'width' => '50px',
                    'type' => 'number',
                    'index' => 'blog_id',
				));

				$this->addColumn('title', array(
                    'header' => Mage::helper('blog')->__('Title'),
                    'index' => 'title',
				));

			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

				return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			   return $this->getUrl('*/*/edit', array('id' => $row->getId()));
		}



		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('blog_id');
			$this->getMassactionBlock()->setFormFieldName('blog_ids');
			$this->getMassactionBlock()->setUseSelectAll(true);
			$this->getMassactionBlock()->addItem('remove_blog', array(
					 'label'=> Mage::helper('blog')->__('Remove blog item'),
					 'url'  => $this->getUrl('*/adminhtml_blog/massRemove'),
					 'confirm' => Mage::helper('blog')->__('Are you sure?')
				));
			return $this;
		}


}