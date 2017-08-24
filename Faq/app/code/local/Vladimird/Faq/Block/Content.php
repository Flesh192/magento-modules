<?php
class Vladimird_Faq_Block_Content extends Mage_Core_Block_Template
{
    public function getCollection()
    {
        return Mage::getModel('vladimird_faq/faq')->getCollection();
    }
}
