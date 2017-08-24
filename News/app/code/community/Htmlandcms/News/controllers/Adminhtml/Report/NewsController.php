<?php

/**
 * Class Htmlandcms_News_Adminhtml_Report_NewsController
 */
class Htmlandcms_News_Adminhtml_Report_NewsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Reports'))
            ->_title($this->__('News Report'));

        $this->loadLayout()
            ->_setActiveMenu('news/news')
            ->_addBreadcrumb(
                Mage::helper('reports')->__('News Report'),
                Mage::helper('reports')->__('News Report')
            )
            ->_addContent($this->getLayout()->createBlock('news/adminhtml_report_news'))
            ->renderLayout();
    }

    public function exportNewsCsvAction()
    {
        $fileName = 'news_report.csv';
        $content = $this->getLayout()->createBlock('news/adminhtml_report_news_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportNewsExcelAction()
    {
        $fileName = 'news_report.xml';
        $content = $this->getLayout()->createBlock('news/adminhtml_report_news_grid')
            ->getExcel($fileName);
        $this->_prepareDownloadResponse($fileName, $content);
    }

}