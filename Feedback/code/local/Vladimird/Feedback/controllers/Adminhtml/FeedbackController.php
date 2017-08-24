<?php
class Vladimird_Feedback_Adminhtml_FeedbackController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
             ->_setActiveMenu('feedback')
             ->_addBreadcrumb(Mage::helper('vladimird_feedback')->__('FeedbackManager'), Mage::helper('vladimird_feedback')->__('Feedback Manager'));
        return $this;
    }
    
    public function indexAction()
    {

        $this->_initAction();
        $this->_addContent($this->getLayout()
             ->createBlock('vladimird_feedback/adminhtml_feedback'));
        $this->renderLayout();
    }
    
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) 
        {
            try 
            {
                $model = Mage::getModel('vladimird_feedback/feedback');
                $model->setFeedback_id($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) 
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/feedback', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
        
    public function massDeleteAction()
    {
        $feedbackIds = $this->getRequest()->getParam('feedback');
        if (!is_array($feedbackIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($feedbackIds as $feedbackId) {
                    $model = Mage::getModel('vladimird_feedback/feedback')->load($feedbackId);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($feedbackIds)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}
