<?php
	
class Htmlandcms_Blog_Block_Adminhtml_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
		public function __construct()
		{

            parent::__construct();
				$this->_objectId = 'cat_id';
				$this->_blockGroup = 'category';
				$this->_controller = 'adminhtml_category';
				$this->_updateButton('save', 'label', Mage::helper('blog')->__('Save item'));
				$this->_updateButton('delete', 'label', Mage::helper('blog')->__('Delete item'));

				$this->_addButton('saveandcontinue', array(
					'label'     => Mage::helper('blog')->__('Save and continue edit'),
					'onclick'   => 'saveAndContinueEdit()',
					'class'     => 'save',
				), -100);



				$this->_formScripts[] = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
		}

		public function getHeaderText()
		{
				if( Mage::registry('blog_data') && Mage::registry('blog_data')->getId() ){

				    return Mage::helper('blog')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('blog_data')->getId()));

				}
				else{

				     return Mage::helper('blog')->__('Add item');

				}
		}

        protected function _prepareLayout() {
            parent::_prepareLayout();
            if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
                $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
            }
        }
}