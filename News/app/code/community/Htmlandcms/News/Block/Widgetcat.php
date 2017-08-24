<?php

/**
 * Class Htmlandcms_News_Block_Widgetcat
 */
class Htmlandcms_News_Block_Widgetcat
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

    /**
     * Get News list
     *
     * @return string
     */

    public function getCategory()
    {
        //var_dump($this->getData('news_count')); die();
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table_cat = $resource->getTableName('news/category');
        $category = $readConnection->fetchAll(
            'SELECT cat_id, name, rewrite_id FROM ' . $table_cat . ' WHERE ' . $table_cat . '.store_id IN ('
            . Mage::app()->getStore()->getId() . ') OR ' . $table_cat . '.store_id = 0 AND ' . $table_cat
            . '.is_active = 1 AND ' . $table_cat . '.parent_id is NULL'
        );
        return $category;

    }

    /**
     * @param $cat_id
     * @return string
     */
    public function getParentCategory($cat_id)
    {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $categoryTable = $resource->getTableName('news/category');
        $select = $read->select()
            ->from($categoryTable, array('cat_id', 'name', 'rewrite_id', 'parent_id'))
            ->where('parent_id=?', $cat_id);
        $categories = $read->fetchAll($select);
        if (!empty($categories)) {
            $html = '<ul>';
            foreach ($categories as $cat) {
                if ($this->getData('news_count') === 'TRUE') :
                    $count = '(' . $this->getNewsCount($cat['cat_id']) . ')';
                endif;
                $html
                    .=
                    '<li><a href="' . Mage::getBaseUrl() . Mage::getModel('core/url_rewrite')->load($cat['rewrite_id'])
                        ->getRequestPath() . '" title="' . $cat['name'] . '">' . $cat['name'] . $count . '</a>';
                $html .= $this->getParentCategory($cat['cat_id']);
                $html .= '</li>';
            }
            $html .= '</ul>';
        }
        return $html;
    }

    /**
     * @param $cat_id
     * @return mixed
     */
    public function getNewsCount($cat_id)
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $newsTable = $resource->getTableName('news/news');
        $count = $readConnection->fetchOne(
            'SELECT COUNT(*) FROM ' . $newsTable . ' WHERE is_active = 1 AND category =' . $cat_id
        );
        return $count;
    }
}