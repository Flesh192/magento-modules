<?php
class Htmlandcms_Blog_IndexController extends Mage_Core_Controller_Front_Action
{
    public function IndexAction()
    {

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table_blog = $resource->getTableName('blog/blog');
        $table_cat = $resource->getTableName('blog/category');
        $blog = $readConnection->fetchAll('SELECT ' . $table_blog . '.* FROM ' . $table_blog . ' JOIN ' . $table_cat . ' ON ' . $table_blog . '.category = ' . $table_cat . '.cat_id WHERE ' . $table_cat . '.store_id IN (' . Mage::app()->getStore()->getId() . ') OR ' . $table_cat . '.store_id = 0');
        /**
         * If no param
         */
        if ($blog == null) {
            Mage::register('nodata', 'There are no blog items');
        }
        Mage::register('blog', $blog);
        $this->loadLayout();
        $this->renderLayout();
    }

    public function viewAction()
    {
        $blog_id = $this->getRequest()->getParam('id');
        if ($blog_id != null && $blog_id != '') {
            $blog = Mage::getModel('blog/blog')->load($blog_id)->getData();
        } else {
            $blog = null;
        }
        /**
         * If no param we load a the last created item
         */
        if ($blog == null) {
            Mage::register('nodata', 'There are no blog items');
        }
        if ($blog['category'] != NULL)
            $blog['parent'] = $this->getChildCat($blog['category']);
        Mage::register('blog', $blog);
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table_comment = $resource->getTableName('blog/comments');
        $select = $readConnection->select()
            ->from($table_comment)
            ->where('blog_id=?', $blog_id);
        $comments = $readConnection->fetchAll($select);
        Mage::register('comments', $comments);
        $this->loadLayout();
        $this->getLayout()->getBlock('blog_view')->setFormAction( Mage::getUrl('*/*/post') );
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }

    public function categoryAction()
    {
        $cat_id = $this->getRequest()->getParam('id');
        if ($cat_id != null && $cat_id != '') {
            $resource = Mage::getSingleton('core/resource');
            $сonnection = $resource->getConnection('core_read');
            $table = $resource->getTableName('blog/blog');
            $select = $сonnection->select()
                ->from($table)
                ->where('category=?', $cat_id);
            $blog = $сonnection->fetchAll($select);
            $table = $resource->getTableName('blog/category');
            $select = $сonnection->select()
                ->from($table)
                ->where('cat_id=?', $cat_id);
            $category = $сonnection->fetchRow($select);
            if ($category['parent_id'] != NULL)
                $category['parent'] = $this->getChildCat($category['parent_id']);
            Mage::register('category', $category);
        }
        /**
         * If no param
         */
        if ($blog == null) {
            Mage::register('nodata', 'There are no blog items');
        }
        Mage::register('blog', $blog);
        $this->loadLayout();
        $this->renderLayout();
    }

    public function getChildCat($parent_cat_id, $category = array(), $i = 0)
    {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $categoryTable = $resource->getTableName('blog_category');
        $select = $read->select()
            ->from($categoryTable, array('cat_id', 'name', 'is_active', 'rewrite_id', 'parent_id'))
            ->where('cat_id=?', $parent_cat_id);
        $categories = $read->fetchAll($select);

        foreach ($categories as $cat) {
            if ($cat['is_active'] == true) {
                $category[] = array('label' => $cat['name'], 'title' => $cat['name'], 'link' => Mage::getBaseUrl() . Mage::getModel('core/url_rewrite')->load($cat['rewrite_id'])->getRequestPath());
                if ($cat['parent_id'] != NULL) {
                    $child_cat = $this->getChildCat($cat['parent_id'], $category, 1);
                    $category = $child_cat;
                }
            }
        }
        return $category;
    }

    public function postAction () {
        $post = $this->getRequest()->getPost();
        if ($post) {
            if (!Mage::getSingleton('customer/session')->isLoggedIn()){
                if (Mage::getStoreConfig('htmlcms_section/blog/active')) {
                    require_once(Mage::getBaseDir('lib').DS.'recaptchalib.php');
                    $privatekey = Mage::getStoreConfig('htmlcms_section/blog/privatekey');
                    $resp = recaptcha_check_answer ($privatekey,
                        $_SERVER["REMOTE_ADDR"],
                        $_POST["recaptcha_challenge_field"],
                        $_POST["recaptcha_response_field"]);
                    if (!$resp->is_valid) {
                        Mage::getSingleton('customer/session')->addError('You entered an invalid CAPTHA! Please choose another.');
                        $resource = Mage::getSingleton('core/resource');
                        $сonnection = $resource->getConnection('core_read');
                        $table = $resource->getTableName('blog/blog');
                        $select = $сonnection->select()
                            ->from($table, array('rewrite_id'))
                            ->where('blog_id=?', (int)$post['blog_id']);
                        $link_id = $сonnection->fetchRow($select);
                        $this->_redirect(Mage::getModel('core/url_rewrite')->load($link_id)->getRequestPath());
                    }
                }
            }
            //var_dump($post); die();

            $model = Mage::getModel('blog/comments')
                ->addData($post)
                ->save();
        }
    }
}
