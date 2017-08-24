<?php

/**
 * Class Htmlandcms_News_Model_Mysql4_Category
 */
class Htmlandcms_News_Model_Mysql4_Category extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("news/category", "cat_id");
    }

}