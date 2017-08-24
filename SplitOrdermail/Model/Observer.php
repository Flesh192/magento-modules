<?php

/**
 * Class Staubli_SplitOrdermail_Model_Observer
 */
class Staubli_SplitOrdermail_Model_Observer
{

    /**
     * @param $observer
     */
    public function sendMail($observer)
	{
		$order = $observer->getEvent()->getOrder();
		Mage::helper('splitordermail')->sendMail($order);
	}
}