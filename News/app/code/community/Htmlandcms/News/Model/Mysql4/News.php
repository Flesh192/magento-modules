<?php

/**
 * Class Htmlandcms_News_Model_Mysql4_News
 */
class Htmlandcms_News_Model_Mysql4_News extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("news/news", "news_id");
    }

    /**
     * The category list is not initialized until the details table is loaded
     * into the array
     *
     * @param Mage_Core_Model_Abstract $object
     *
     * @return \Mage_Core_Model_Abstract
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('news/news_category'))
            ->where('cat_id = ?', $object->getCategory());

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $details_array = array();
            foreach ($data as $row) {
                $details_array[$row['cat_id']] = $row['name'];
            }
            $object->setData('category', $details_array);
        }

        return parent::_afterLoad($object);
    }
}