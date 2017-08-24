<?php

/**
 * Class Htmlandcms_News_Adminhtml_NewsController
 */
class Htmlandcms_News_Adminhtml_NewsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('news');
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('News'));
        $this->_title($this->__('Manager News'));
        $this->_initAction();
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_title($this->__('News'));
        $this->_title($this->__('Edit Item'));

        $id = $this->getRequest()->getParam('id');
        $news_data = Mage::getModel('news/news')->load($id);
        if ($news_data->getId()) {
            $rew_url = $this->getRewrite($news_data->getData('rewrite_id'));
            Mage::register('rewrite_url', $rew_url['request_path']);
            Mage::register('news_data', $news_data);
            Mage::register('news_category', $this->getCategory());
            $this->loadLayout();
            $this->_setActiveMenu('news/news');
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('News Manager'), Mage::helper('adminhtml')->__('News Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('News Description'), Mage::helper('adminhtml')->__('News Description')
            );
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('news/adminhtml_news_edit'))->_addLeft(
                $this->getLayout()->createBlock('news/adminhtml_news_edit_tabs')
            );
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('news')->__('Item does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {

        $this->_title($this->__('News'));
        $this->_title($this->__('News'));
        $this->_title($this->__('New Item'));

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('news/news')->load($id);

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        Mage::register('news_category', $this->getCategory());

        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('news_data', $model);

        $this->loadLayout();
        $this->_setActiveMenu('news/news');

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addBreadcrumb(
            Mage::helper('adminhtml')->__('News Manager'), Mage::helper('adminhtml')->__('News Manager')
        );
        $this->_addBreadcrumb(
            Mage::helper('adminhtml')->__('News Description'), Mage::helper('adminhtml')->__('News Description')
        );


        $this->_addContent($this->getLayout()->createBlock('news/adminhtml_news_edit'))->_addLeft(
            $this->getLayout()->createBlock('news/adminhtml_news_edit_tabs')
        );
        $this->renderLayout();

    }

    public function saveAction()
    {

        $post_data = $this->getRequest()->getPost();
        $exist_model = Mage::getModel('news/news')->load($this->getRequest()->getParam('id'));

        if ($post_data) {

            try {

                if ((isset($_FILES['image']['name']) and file_exists($_FILES['image']['tmp_name']))
                    or isset($post_data['image']['delete'])
                ) {
                    // remove exist images
                    if ($exist_model->hasData('image') and $exist_model->getData('image') != '') {
                        $this->removeImage($exist_model->getData('image'));
                    }

                    if (isset($_FILES['image']['name']) and (file_exists($_FILES['image']['tmp_name']))) {
                        try {
                            $uploader = new Varien_File_Uploader('image');
                            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png')); // or pdf or anything
                            $uploader->setAllowRenameFiles(true);
                            $uploader->setFilesDispersion(false);

                            $path = Mage::getBaseDir('media') . DS . 'image_news';
                            $errorSim = array(' ', '\/', '\'', ':', '?', '{', '}', '|', '\\', '^', '[', ']', '`', ';',
                                              '?', '@', '&', '=', '+', '$', ',');
                            $fileName = Varien_File_Uploader::getNewFileName(
                                $path . DS . str_replace($errorSim, '-', $_FILES['image']['name'])
                            );
                            $post_data['image'] = $fileName;

                            $uploader->save($path, $fileName);

                            $srcPath = $path . DS . $fileName;
                            $destPath = $path . DS . 'thumb' . DS . $fileName;
                            $this->saveImageThumbnail($srcPath, $destPath, 220, 140);
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
                    $path = Mage::getBaseDir('media') . DS . 'image_news';
                    $thumb_check_path = $path . DS . 'thumb' . DS . $exist_model->getData('image');
                    if (!file_exists($thumb_check_path) && file_exists($exist_model->getData('image'))) {
                        $srcPath = $path . DS . $exist_model->getData('image');
                        $destPath = $path . DS . 'thumb' . DS . $exist_model->getData('image');
                        $this->saveImageThumbnail($srcPath, $destPath, 220, 140);
                    }
                }

                if (isset ($post_data['url'])) {
                    $rewrite_url = $post_data['url'];
                    unset ($post_data['url']);
                }


                $model = Mage::getModel('news/news')
                    ->addData($post_data)
                    ->setId($this->getRequest()->getParam('id'))
                    ->save();

                $news_id = $model->getId();
                Mage::getModel('core/url_rewrite')
                    ->setId($exist_model->getData('rewrite_id'))
                    ->setRequestPath($rewrite_url)
                    ->setStoreId(Mage::app()->getStore()->getStoreId())
                    ->setIdPath('news/index/view/id/' . $news_id)
                    ->setTargetPath('news/index/view/id/' . $news_id)
                    ->setIsSystem(false)
                    ->save();

                $post_data['rewrite_id'] = Mage::getModel('core/url_rewrite')
                    ->loadByIdPath('news/index/view/id/' . $news_id)
                    ->getId();

                $model = Mage::getModel('news/news')
                    ->addData($post_data)
                    ->setId($news_id)
                    ->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('News was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setNewsData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setNewsData($this->getRequest()->getPost());
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
                $model = Mage::getModel('news/news');
                $exist_model = $model->load($this->getRequest()->getParam('id'));
                //delete image if exist
                if ($exist_model->hasData('image') and $exist_model->getData('image') != '') {
                    $this->removeImage($exist_model->getData('image'));
                }
                //delete url rewrites
                Mage::getModel('core/url_rewrite')
                    ->setId($exist_model->getData('rewrite_id'))
                    ->delete();
                $model->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Item was successfully deleted')
                );
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
            $ids = $this->getRequest()->getPost('news_ids', array());
            foreach ($ids as $id) {
                $model = Mage::getModel('news/news');
                $exist_model = $model->load($this->getRequest()->getParam('id'));
                if ($exist_model->hasData('image') and $exist_model->getData('image') != '') {
                    $this->removeImage($exist_model->getData('image'));
                }
                $model->setId($id)->delete();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('adminhtml')->__('Item(s) was successfully removed')
            );
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
        $fileName = 'news.xml';
        $grid = $this->getLayout()->createBlock('news/adminhtml_news_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    /**
     * Create thumbnail from image
     *
     * @param string $srcPath
     * @param string $destPath
     *
     * @return Varien_Image
     */
    private function saveImageThumbnail($srcPath, $destPath, $width = 220, $height = 140)
    {
        $image = new Varien_Image($srcPath);
        // you cannot use method chaining with Varien_Image
        $image->constrainOnly(false);
        $image->keepFrame(false);
        // avoid black borders by setting background colour
        $image->backgroundColor(array(255, 255, 255));
        $image->keepAspectRatio(true);
        $image->resize($width, $height);
        $image->save($destPath);
        return $image;
    }

    /**Get category list
     *
     * @return array
     */
    public function getCategory()
    {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $categoryTable = $resource->getTableName('news_category');
        $select = $read->select()
            ->from($categoryTable, array('cat_id', 'name', 'store_id'));
        $categories = $read->fetchAll($select);
        $category = array();
        foreach ($categories as $cat) {
            $stores = explode(',', $cat['store_id']);
            foreach ($stores as $key => $store_id) {
                $stores[$key] = Mage::getModel('core/store')->load($store_id)->getName();
            }
            $stores = implode(', ', $stores);
            $category[] = array('value' => $cat['cat_id'], 'label' => $cat['name'] . ' (' . $stores . ')');
        }
        return $category;
    }

    /** Get rewrite url
     *
     * @param $rew_id
     *
     * @return mixed
     */
    public function getRewrite($rew_id)
    {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $categoryTable = $resource->getTableName('core_url_rewrite');
        $select = $read->select()
            ->from($categoryTable, array('request_path'))
            ->where('url_rewrite_id=?', $rew_id);
        $rew_url = $read->fetchRow($select);

        return $rew_url;
    }


    /**
     * @param $imageName
     */
    public function removeImage($imageName)
    {
        foreach (
            glob(Mage::getBaseDir('media') . DS . 'image_news' . DS . 'widget' . DS . '*' . $imageName) as $filename
        ) {
            unlink($filename);
        }
        $image_path = Mage::getBaseDir('media') . DS . 'image_news' . DS . $imageName;
        if (file_exists($image_path)) {
            unlink($image_path);
        }
        $thumb_path = Mage::getBaseDir('media') . DS . 'image_news' . DS . 'thumb' . DS . $imageName;
        if (file_exists($thumb_path)) {
            unlink($thumb_path);
        }

    }
}