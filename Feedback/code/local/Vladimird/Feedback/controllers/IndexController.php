<?php
class Vladimird_Feedback_IndexController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $this->loadLayout();
        $data = $this->getLayout()->getBlock('feedback')
            ->setFormAction( Mage::getUrl('*/*/post') );

        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
                 
    }

    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        if ( $post ) {
            /*CAPTCHA
            * private key: 6Ldv9tMSAAAAAPL3ZDzB8siNc--m3Z4NwJ_AMAXo
            * public Key: 6Ldv9tMSAAAAAJzMjtqvfrwmEHNu9suEh3FDzl5L*/
            Mage::getSingleton('core/session')->setName($post['name'])
                                              ->setEmail($post['email'])
                                              -> setComment($post['comment'])
                                              -> setTelephone($post['telephone']);
            
            if (!Mage::getSingleton('customer/session')->isLoggedIn()){
                require_once('recaptchalib.php');
                $privatekey = "6Ldv9tMSAAAAAPL3ZDzB8siNc--m3Z4NwJ_AMAXo";
                $resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);
                if (!$resp->is_valid) {
                    Mage::getSingleton('customer/session')->addSuccess(Mage::helper('vladimird_feedback')->__('You entered an invalid CAPTHA! Please choose another.'));
                    $this->_redirect('*/*/');
                }
                //die('save1');
            }                
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);
            try {
                $postObject = new Varien_Object();
                $postObject->setData($post);
                    
                $error = false;
                    
                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }
                    
                if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
                    $error = true;
                }
                    
                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }
                    
                if ($error) {
                    throw new Exception();
                }
                    
                $model = Mage::getModel('vladimird_feedback/feedback');
                $model -> setName($post['name'])
                        -> setEmail($post['email'])
                        -> setComment($post['comment'])
                        -> setReason($post['reason'])
                        -> setTelephone($post['telephone'])
                        ->save();

                if (!$model->getName()) {
                    throw new Exception();
                }
                    
                $translate->setTranslateInline(true);
                    
                Mage::getSingleton('customer/session')->addSuccess(Mage::helper('vladimird_feedback')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                $this->_redirect('*/*/');
                    
                return;
                    
            } catch (Exception $e) {
                $translate->setTranslateInline(true);
                Mage::getSingleton('customer/session')->addError(Mage::helper('vladimird_feedback')->__('Unable to submit your request. Please, try again later'));
                $this->_redirect('*/*/');
                return;
            }
        }
    }
}

