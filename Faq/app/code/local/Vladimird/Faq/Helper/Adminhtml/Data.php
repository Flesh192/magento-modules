<?php
class Vladimird_Faq_Helper_Adminhtml_Data extends Mage_Core_Helper_Abstract
{
    public function getAfterElementHtml()
    {
        $html = parent::getAfterElementHtml();
        if ($this->getIsWysiwygEnabled()) {
            $html .= Mage::getSingleton('core/layout')
                ->createBlock('adminhtml/widget_button', '', array(
                    'label'   => Mage::helper('catalog')->__('WYSIWYG Editor'),
                    'type'    => 'button',
                    'onclick' => 'catalogWysiwygEditor.open(\''.Mage::helper('adminhtml')->getUrl('*/*/wysiwyg').'\', \''.$this->getHtmlId().'\')',
					'style'   => 'display:block;'
                ))->toHtml();
        }
        return $html;
    }

    public function getIsWysiwygEnabled()
    {
        return (bool)(Mage::getSingleton('cms/wysiwyg_config')->isEnabled() && $this->getEntityAttribute()->getIsWysiwygEnabled());
    }
}
