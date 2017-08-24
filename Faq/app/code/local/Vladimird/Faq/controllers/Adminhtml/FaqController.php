<?php
class Vladimird_Faq_Adminhtml_FaqController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
             ->_setActiveMenu('cms/faq');
        return $this;
    }
    
    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }
    
    public function editAction()
    {
        $this->_title($this->__('Edit FAQ'));

        $this->loadLayout()
             ->_setActiveMenu('cms/faq');
        $this->renderLayout();
    }
    
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        if (!empty($data)) {
            try {
                Mage::getModel('vladimird_faq/faq')->setData($data)
                    ->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vladimird_faq')->__('FAQ successfully saved'));
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError($this->__('Somethings went wrong'));
            }
        }
        return $this->_redirect('*/*/');
    }
    
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) 
        {
            try 
            {
                $model = Mage::getModel('vladimird_faq/faq');
                $model->setFaq_id($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) 
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/faq', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
        
    public function massDeleteAction()
    {
        $faqIds = $this->getRequest()->getParam('faq');
        if (!is_array($faqIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($faqIds as $faqkId) {
                    $model = Mage::getModel('vladimird_faq/faq')->load($faqkId);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($faqIds)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}
