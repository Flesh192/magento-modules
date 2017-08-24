<?php
class Htmlandcms_Blog_Model_Mysql4_Category extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('blog/category', 'cat_id');
    }

}