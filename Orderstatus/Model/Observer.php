<?php

class Staubli_Orderstatus_Model_Observer
{

    public function addOrderstatusAction($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View) {
            $message = Mage::helper('sales')->__('Are you sure you want to Finish this order?');

            $block->addButton('finish',
                array( 'label' => Mage::helper('sales')->__('Finish'),
                       'onclick' => "confirmSetLocation('{$message}', '{$block->getUrl('orderstatus/adminhtml_index/finish')}')", 'class' => 'go' ));

        }
    }
}
