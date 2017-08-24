<?php

/**
 * Class Htmlandcms_News_IndexController
 */
class Htmlandcms_News_IndexController extends Mage_Core_Controller_Front_Action
{

    public function IndexAction()
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table_news = $resource->getTableName('news/news');
        $table_cat = $resource->getTableName('news/category');
        $news = $readConnection->fetchAll(
            'SELECT ' . $table_news . '.* FROM ' . $table_news . ' JOIN ' . $table_cat . ' ON ' . $table_news
            . '.category = ' . $table_cat . '.cat_id WHERE ' . $table_cat . '.store_id IN (' . Mage::app()->getStore()
                ->getId() . ') OR ' . $table_cat . '.store_id = 0'
        );
        /**
         * If no param
         */
        if ($news == null) {
            Mage::register('nodata', 'There are no news');
        }
        Mage::register('news', $news);
        $this->loadLayout();
        $this->renderLayout();
    }

    public function viewAction()
    {
        $news_id = $this->getRequest()->getParam('id');
        if ($news_id != null && $news_id != '') {
            $news = Mage::getModel('news/news')->load($news_id)->getData();
        } else {
            $news = null;
        }
        /**
         * If no param we load a the last created item
         */
        if ($news == null) {
            Mage::register('nodata', 'There are no news');
        }
        if ($news['category'] != null) {
            $news['parent'] = $this->getChildCat($news['category']);
        }
        Mage::register('news', $news);
        $this->loadLayout();
        $this->renderLayout();
    }

    public function categoryAction()
    {
        $cat_id = $this->getRequest()->getParam('id');
        if ($cat_id != null && $cat_id != '') {
            $resource = Mage::getSingleton('core/resource');
            $сonnection = $resource->getConnection('core_read');
            $table = $resource->getTableName('news/news');
            $select = $сonnection->select()
                ->from($table)
                ->where('category=?', $cat_id);
            $news = $сonnection->fetchAll($select);
            $table = $resource->getTableName('news/category');
            $select = $сonnection->select()
                ->from($table)
                ->where('cat_id=?', $cat_id);
            $category = $сonnection->fetchRow($select);
            if ($category['parent_id'] != null) {
                $category['parent'] = $this->getChildCat($category['parent_id']);
            }
            Mage::register('category', $category);
        }
        /**
         * If no param
         */
        if ($news == null) {
            Mage::register('nodata', 'There are no news');
        }
        Mage::register('news', $news);
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * @param $parent_cat_id
     * @param array $category
     * @param int $i
     * @return array
     */
    public function getChildCat($parent_cat_id, $category = array(), $i = 0)
    {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $categoryTable = $resource->getTableName('news/category');
        $select = $read->select()
            ->from($categoryTable, array('cat_id', 'name', 'is_active', 'rewrite_id', 'parent_id'))
            ->where('cat_id=?', $parent_cat_id);
        $categories = $read->fetchAll($select);

        foreach ($categories as $cat) {
            if ($cat['is_active'] == true) {
                $category[] = array('label' => $cat['name'], 'title' => $cat['name'], 'link' =>
                    Mage::getBaseUrl() . Mage::getModel('core/url_rewrite')->load($cat['rewrite_id'])->getRequestPath(
                    ));
                if ($cat['parent_id'] != null) {
                    $child_cat = $this->getChildCat($cat['parent_id'], $category, 1);
                    $category = $child_cat;
                }
            }
        }
        return $category;
    }
}
