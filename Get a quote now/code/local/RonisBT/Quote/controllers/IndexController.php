<?php
class RonisBT_Quote_IndexController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $page = Mage::getSingleton('core/session')->getPage();        
        if (!isset($page)){
            
            Mage::getSingleton('core/session')->addError(Mage::helper('quote')->__('Please, select product first'));
            return;
        }
        
        $this->loadLayout();
        $data = $this->getLayout()->getBlock('quote')
                                ->setFormAction( Mage::getUrl('*/*/post') );
        
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();                 
    }
    
    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        if ( $post ) {
            
             
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);
            try {
                $postObject = new Varien_Object();
                $postObject->setData($post);
                
                $error = false;
                              
                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }
                   
                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }
                
                if (!Zend_Validate::is(trim($post['page']) , 'NotEmpty')) {
                    $error = true;
                }

                if ($error) {
                    throw new Exception();
                }
                
                $name = $post['name'];
                $email = $post['email'];
                $page_from = $post['page'];
                $phone = $post['phone'];
                $comment = $post['text'];
                
                $to = Mage::getStoreConfig('quote_option/admin_email/email');
                $from = Mage::getStoreConfig('quote_option/admin_email/from');
                $email_from = Mage::getStoreConfig('quote_option/admin_email/efrom');
                $subject = "Quote";
                $headers= "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
                $headers .= "From:".$from." <".$email_from.">\r\n";
                $message = "Name: ".$name."<br/>
                            e-mail: ".$email."<br/>
                            Phone: ".$phone."<br/>
                            Product page: ".$page_from."<br/>
                            Comment: ".$comment;

                if (!mail($to, $subject, $message, $headers)) {
                    throw new Exception();
                }                
                
                $translate->setTranslateInline(true);
                    
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('quote')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                $this->_redirectUrl($page_from);
                    
                return;
                    
            } catch (Exception $e) {
                $translate->setTranslateInline(true);
                Mage::getSingleton('core/session')->addError(Mage::helper('quote')->__('Unable to submit your request. Please, try again later'));
                $this->_redirect('*/*/');
                return;
            }
        } else {
            Mage::getSingleton('core/session')->addError(Mage::helper('quote')->__('Unable to submit your request. Please, try again later'));
            return;
        }
    }
}

