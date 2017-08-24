<?php

/**
 * Class Htmlandcms_News_Block_Widgetlist
 */
class Htmlandcms_News_Block_Widgetlist
    extends Mage_Core_Block_Template //_Abstract
    implements Mage_Widget_Block_Interface
{
    /**
     * Initialization
     */
    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * @return mixed
     */
    protected function _toHtml()
    {
        return parent::_toHtml();
    }

    private $_newses;

    /**
     * Get News list
     *
     * @return string
     */
    public function getNewsList()
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table_news = $resource->getTableName('news/news');
        $table_cat = $resource->getTableName('news/category');
        $this->_newses = $readConnection->fetchAll(
            'SELECT ' . $table_news . '.* FROM ' . $table_news . ' JOIN ' . $table_cat . ' ON ' . $table_news
            . '.category = ' . $table_cat . '.cat_id WHERE ' . $table_cat . '.store_id IN (' . Mage::app()->getStore()
                ->getId() . ') OR ' . $table_cat . '.store_id = 0' . ' LIMIT ' . $this->getData('count')
        );
        return $this->_newses;
    }

    /* @resize image for widget
     * return link
     */
    public function getRightSizeImage($id)
    {
        $exist_model = Mage::getModel('news/news')->load($id);
        $htmlSize = $this->getData('width') . '-' . $this->getData('height') . '-';
        $path = Mage::getBaseDir('media') . DS . 'image_news';
        $destPathWidget = $path . DS . 'widget' . DS . $htmlSize . $exist_model->getData('image');
        if (!file_exists($destPathWidget)) {
            if ($exist_model->hasData('image') and $exist_model->getData('image') != '') {
                $srcPath = $path . DS . $exist_model->getData('image');
                $this->saveImageThumbnail($srcPath, $destPathWidget);
            }
        }
        $link = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'image_news/widget/' . $htmlSize
            . $exist_model->getData('image');
        return $link;
    }

    /** Save thumbnail in image_news/widget
     *
     * @param $srcPath  - image path
     * @param $destPath - save path
     *
     * @return Varien_Image
     */
    private function saveImageThumbnail($srcPath, $destPath)
    {
        $image = new Varien_Image($srcPath);
        // you cannot use method chaining with Varien_Image
        $image->constrainOnly(false);
        $image->keepFrame(false);
        // avoid black borders by setting background colour
        $image->backgroundColor(array(255, 255, 255));
        $image->keepAspectRatio(true);
        $image->resize($this->getData('width'), $this->getData('height'));
        $image->save($destPath);
        return $image;
    }
}