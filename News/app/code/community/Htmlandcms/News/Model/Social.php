<?php

/**
 * Class Htmlandcms_News_Model_Social
 */
class Htmlandcms_News_Model_Social extends Mage_Core_Model_Abstract
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'disable', 'label' => Mage::helper('news')->__('Disable')),
            array('value' => 'facebook', 'label' => Mage::helper('news')->__('Facebook')),
            array('value' => 'twitter', 'label' => Mage::helper('news')->__('Twitter')),
            array('value' => 'google', 'label' => Mage::helper('news')->__('Google+')),
            array('value' => 'pin', 'label' => Mage::helper('news')->__('Pin')),
            array('value' => 'print', 'label' => Mage::helper('news')->__('Print'))
        );
    }
}