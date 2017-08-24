<?php

class Htmlandcms_Blog_Adminhtml_BlogController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('cms/blog');
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Blog'));
        $this->_title($this->__('Manager blog'));
        $this->_initAction();
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_title($this->__('Blog'));
        $this->_title($this->__('Edit blog item'));
        
        $id = $this->getRequest()->getParam('id');
        $blog_data = Mage::getModel('blog/blog')->load($id);
        if ($blog_data->getId()) {
            $rew_url = $this->getRewrite($blog_data->getData('rewrite_id'));
            Mage::register('rewrite_url', $rew_url['request_path']);
            Mage::register('blog_data', $blog_data);
            Mage::register('blog_category', $this->getCategory());
            $this->loadLayout();
            $this->_setActiveMenu('blog/blog');
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Blog manager'), Mage::helper('adminhtml')->__('Blog manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Blog Description'), Mage::helper('adminhtml')->__('Blog description'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('blog/adminhtml_blog_edit'))->_addLeft($this->getLayout()->createBlock('blog/adminhtml_blog_edit_tabs'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blog')->__('Item does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {

        $this->_title($this->__('Blog'));
        $this->_title($this->__('Blog item'));

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('blog/blog')->load($id);

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        Mage::register('blog_category', $this->getCategory());

        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('blog_data', $model);

        $this->loadLayout();
        $this->_setActiveMenu('blog/blog');

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Blog manager'), Mage::helper('adminhtml')->__('Blog manager'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Blog description'), Mage::helper('adminhtml')->__('Blog description'));


        $this->_addContent($this->getLayout()->createBlock('blog/adminhtml_blog_edit'))->_addLeft($this->getLayout()->createBlock('blog/adminhtml_blog_edit_tabs'));
        $this->renderLayout();

    }

    public function saveAction()
    {

        $post_data = $this->getRequest()->getPost();
        $exist_model = Mage::getModel('blog/blog')->load($this->getRequest()->getParam('id'));

        if ($post_data) {

            try {

                if ((isset($_FILES['image']['name']) and file_exists($_FILES['image']['tmp_name'])) or isset($post_data['image']['delete'])) {
                    // remove exist images
                    if ($exist_model->hasData('image') and $exist_model->getData('image') != '') {
                        $image_path = Mage::getBaseDir('media') . DS . 'image_blog' . DS . $exist_model->getData('image');
                        if (file_exists($image_path))
                            unlink($image_path);

                        $thumb_path = Mage::getBaseDir('media') . DS . 'image_blog' . DS . 'thumb' . DS . $exist_model->getData('image');
                        if (file_exists($thumb_path))
                            unlink($thumb_path);
                    }

                    if (isset($_FILES['image']['name']) and (file_exists($_FILES['image']['tmp_name']))) {
                        try {
                            $uploader = new Varien_File_Uploader('image');
                            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png')); // or pdf or anything
                            $uploader->setAllowRenameFiles(true);
                            $uploader->setFilesDispersion(false);

                            $path = Mage::getBaseDir('media') . DS . 'image_blog';
                            $errorSim = array(' ', '\/', '\'', ':', '?', '{', '}' , '|', '\\','^', '[' , ']' , '`' , ';' , '?' , '@' , '&' , '=' , '+' , '$' , ',');
                            $fileName = Varien_File_Uploader::getNewFileName($path . DS . str_replace($errorSim, '-', $_FILES['image']['name']));
                            $post_data['image'] = $fileName;

                            $uploader->save($path, $fileName);

                            $srcPath = $path . DS . $fileName;
                            $destPath = $path . DS . 'thumb' . DS . $fileName;
                            $this->saveImageThumbnail($srcPath, $destPath);
                        } catch (Exception $e) {

                        }
                    } elseif (isset($post_data['image']['delete'])) {
                        $post_data['image'] = '';
                    }
                } elseif (isset($post_data['image']['value'])) {
                    // 'cause magento set image[value] in POST need to unset it
                    unset($post_data['image']);
                }

                // Creating thumbnail if not exists
                if ($exist_model->hasData('image') and $exist_model->getData('image') != '') {
                    $path = Mage::getBaseDir('media') . DS . 'image_blog';
                    $thumb_check_path = $path . DS . 'thumb' . DS . $exist_model->getData('image');
                    if (!file_exists($thumb_check_path) && file_exists($exist_model->getData('image'))) {
                        $srcPath = $path . DS . $exist_model->getData('image');
                        $destPath = $path . DS . 'thumb' . DS . $exist_model->getData('image');
                        $this->saveImageThumbnail($srcPath, $destPath);
                    }
                }

                if (isset ($post_data['url'])) {
                    $rewrite_url = $post_data['url'];
                    unset ($post_data['url']);
                }


                $model = Mage::getModel('blog/blog')
                    ->addData($post_data)
                    ->setId($this->getRequest()->getParam('id'))
                    ->save();

                $blog_id = $model->getId();
                Mage::getModel('core/url_rewrite')
                    ->setId($exist_model->getData('rewrite_id'))
                    ->setRequestPath($rewrite_url)
                    ->setStoreId(Mage::app()->getStore()->getStoreId())
                    ->setIdPath('blog/index/view/id/'.$blog_id)
                    ->setTargetPath('blog/index/view/id/'.$blog_id)
                    ->setIsSystem(false)
                    ->save();

                $post_data['rewrite_id'] = Mage::getModel('core/url_rewrite')
                    ->loadByIdPath('blog/index/view/id/'.$blog_id)
                    ->getId();

                $model = Mage::getModel('blog/blog')
                    ->addData($post_data)
                    ->setId($blog_id)
                    ->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Blog item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setBlogData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setBlogData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }


    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('blog/blog');
                $model->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }


    public function massRemoveAction()
    {
        try {
            $ids = $this->getRequest()->getPost('blog_ids', array());
            foreach ($ids as $id) {
                $model = Mage::getModel('blog/blog');
                $model->setId($id)->delete();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item(s) was successfully removed'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }

    /**
     *  Export order grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName = 'blog.xml';
        $grid = $this->getLayout()->createBlock('blog/adminhtml_blog_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    /**
     * Create thumbnail from image
     *
     * @param string $srcPath
     * @param string $destPath
     * @return Varien_Image
     */
    public function saveImageThumbnail($srcPath, $destPath)
    {
        $image = new Varien_Image($srcPath);
        // you cannot use method chaining with Varien_Image
        $image->constrainOnly(false);
        $image->keepFrame(false);
        // avoid black borders by setting background colour
        $image->backgroundColor(array(255, 255, 255));
        $image->keepAspectRatio(true);
        $image->resize(220, 140);
        $image->save($destPath);
        return $image;
    }

    public function getCategory() {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $categoryTable = $resource->getTableName('blog_category');
        $select = $read->select()
            ->from($categoryTable, array('cat_id', 'name', 'store_id'));
        $categories = $read->fetchAll($select);
        $category = array();
        foreach ($categories as $cat) {
            $stores = explode (',', $cat['store_id']);
            foreach ($stores as $key=>$store_id)
                $stores[$key] = Mage::getModel('core/store')->load($store_id)->getName();
            $stores = implode (', ', $stores);
            $category[] = array('value' => $cat['cat_id'], 'label' => $cat['name'].' ('.$stores.')');
        }
        return $category;
    }

    public function getRewrite($rew_id) {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $categoryTable = $resource->getTableName('core_url_rewrite');
        $select = $read->select()
            ->from($categoryTable, array('request_path'))
            ->where('url_rewrite_id=?', $rew_id);
        $rew_url = $read->fetchRow($select);

        return $rew_url;
    }
}