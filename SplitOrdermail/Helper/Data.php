<?php

/**
 * Class Staubli_SplitOrdermail_Helper_Data
 */
class Staubli_SplitOrdermail_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @param Mage_Sales_Model_Order $order
     * @return bool
     */
    public function sendMail(Mage_Sales_Model_Order $order)
    {
        $orderProducts = array();
        /* @var $item Mage_Sales_Model_Order_Item */
        foreach($order->getAllVisibleItems() as $item)
        {
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            $stockLocation = $product->getAttributeText('st_stocklocation');

            $orderProducts[$stockLocation]['items'][] = $item;
            if(!isset($orderProducts[$stockLocation]['contact']))
            {
                $orderProducts[$stockLocation]['contact'] = $product->getAttributeText('st_stocklocation_contact');
            }
        }

        foreach($orderProducts as $splitOrder)
        {
            $storeId = $order->getStore()->getId();

            /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
            $mailer = Mage::getModel('core/email_template_mailer');
            /** @var $emailInfo Mage_Core_Model_Email_Info */
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($splitOrder['contact']);

            //Add customer email
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $email = $customer->getEmail();
            $emailInfo->addTo($email);

            //Add shop imail by request
            $emailInfo->addTo('shop@staubli-brandstore');

            $mailer->addEmailInfo($emailInfo);
            
            // Start store emulation process
            /** @var $appEmulation Mage_Core_Model_App_Emulation */
            $appEmulation = Mage::getSingleton('core/app_emulation');
            $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

            try {
                // Retrieve specified view block from appropriate design package (depends on emulated store)
                $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
                    ->setIsSecureMode(true);
                $paymentBlock->getMethod()->setStore($storeId);
                $paymentBlockHtml = $paymentBlock->toHtml();
            } catch (Exception $exception) {
                // Stop store emulation process
                $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
                throw $exception;
            }

            $emailTemplate  = Mage::getModel('core/email_template')->loadByCode('Staubli SplitOrdermail');
            $templateId = $emailTemplate->getId();
            // Set all required params and send emails
            $mailer->setSender(Mage::getStoreConfig($order::XML_PATH_EMAIL_IDENTITY, $storeId));
            $mailer->setStoreId($order->getStore()->getId());
            $mailer->setTemplateId($templateId);
            $mailer->setTemplateParams(array(
                'order'        => $order,
                'billing'      => $order->getBillingAddress(),
                'payment_html' => $paymentBlockHtml,
                'items'        => $splitOrder['items']
            ));
            $mailer->send();
        }
        return true;
    }
}
	 
