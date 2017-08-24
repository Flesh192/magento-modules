<?php

/**
 * Class Htmlandcms_News_Block_Adminhtml_Category_Edit
 */
class Htmlandcms_News_Block_Adminhtml_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Htmlandcms_News_Block_Adminhtml_Category_Edit constructor.
     */
    public function __construct()
    {

        parent::__construct();
        $this->_objectId = "cat_id";
        $this->_blockGroup = "category";
        $this->_controller = "adminhtml_category";
        $this->_updateButton("save", "label", Mage::helper("news")->__("Save Item"));
        $this->_updateButton("delete", "label", Mage::helper("news")->__("Delete Item"));

        $this->_addButton(
            "saveandcontinue", array(
                                    "label"   => Mage::helper("news")->__("Save And Continue Edit"),
                                    "onclick" => "saveAndContinueEdit()",
                                    "class"   => "save",
                               ), -100
        );


        $this->_formScripts[]
            = "

							function saveAndContinueEdit(){
								editForm.submit($('edit_form').action+'back/edit/');
							}
						";
    }

    /**
     * @return mixed
     */
    public function getHeaderText()
    {
        if (Mage::registry("news_data") && Mage::registry("news_data")->getId()) {

            return Mage::helper("news")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("news_data")->getId()));

        } else {

            return Mage::helper("news")->__("Add Item");

        }
    }

    /**
     * @return render layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }
}