<?php

class Htmlandcms_Blog_Adminhtml_CategoryController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
		{
                $this->loadLayout()->_setActiveMenu('cms/category');
				return $this;
		}

		public function indexAction() 
		{
            $this->_title($this->__('Category'));
			$this->_title($this->__('Manager blog category'));
            $this->_initAction();
			$this->renderLayout();
		}

		public function editAction()
		{
				$this->_title($this->__('Blog'));
			    $this->_title($this->__('Edit item'));
				$id = $this->getRequest()->getParam('id');
				$model = Mage::getModel('blog/category')->load($id);
				if ($model->getId()) {
                    $rew_url = $this->getRewrite($model->getData('rewrite_id'));
                    Mage::register('rewrite_url', $rew_url['request_path']);
                    Mage::register('cat_list', $this->getCategoryList());
					Mage::register('category_data', $model);
					$this->loadLayout();
					$this->_setActiveMenu('cms/category');
					$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Blog Manager'), Mage::helper('adminhtml')->__('Category blog'));
					$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Category description'), Mage::helper('adminhtml')->__('Category description'));
					$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
					$this->_addContent($this->getLayout()->createBlock('blog/adminhtml_category_edit'))->_addLeft($this->getLayout()->createBlock('blog/adminhtml_category_edit_tabs'));
					$this->renderLayout();
				} 
				else {
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blog')->__('Item does not exist.'));
					$this->_redirect('*/*/');
				}
		}

		public function newAction()
		{

            $this->_title($this->__('Blog'));
            $this->_title($this->__('Blog item'));

            $id   = $this->getRequest()->getParam('id');
            $model  = Mage::getModel('blog/blog')->load($id);

            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('blog_data', $model);
            Mage::register('cat_list', $this->getCategoryList());

            $this->loadLayout();
            $this->_setActiveMenu('blog/blog');

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Blog Manager'), Mage::helper('adminhtml')->__('Blog Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Blog Description'), Mage::helper('adminhtml')->__('Blog Description'));


            $this->_addContent($this->getLayout()->createBlock('blog/adminhtml_category_edit'))->_addLeft($this->getLayout()->createBlock('blog/adminhtml_category_edit_tabs'));

            $this->renderLayout();

		}

		public function saveAction()
		{

			$post_data=$this->getRequest()->getPost();
            $exist_model = Mage::getModel('blog/category')->load($this->getRequest()->getParam('id'));


			if ($post_data) {
                    try {
                        if (isset ($post_data['url'])) {
                            $rewrite_url = $post_data['url'];
                            unset ($post_data['url']);
                        }

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

                        $model = Mage::getModel('blog/category')
                            ->addData($post_data)
                            ->setId($this->getRequest()->getParam('id'))
                            ->save();

                        if(isset($post_data['stores'])) {
                            if(in_array('0', $post_data['stores'])){
                                $post_data['store_id'] = '0';
                            }
                            else{
                                $post_data['store_id'] = implode(',', $post_data['stores']);
                            }
                            unset($post_data['stores']);
                        }

                        $cat_id = $model->getId();
                        Mage::getModel('core/url_rewrite')
                            ->setId($exist_model->getData('rewrite_id'))
                            ->setRequestPath($rewrite_url)
                            ->setStoreId(Mage::app()->getStore()->getStoreId())
                            ->setIdPath('blog/index/category/id/'.$cat_id)
                            ->setTargetPath('blog/index/category/id/'.$cat_id)
                            ->setIsSystem(false)
                            ->save();

                        $post_data['rewrite_id'] = Mage::getModel('core/url_rewrite')
                            ->loadByIdPath('blog/index/category/id/'.$cat_id)
                            ->getId();

                        $model = Mage::getModel('blog/category')
                            ->addData($post_data)
                            ->setId($cat_id)
                            ->save();

                        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Category was successfully saved'));
                        Mage::getSingleton('adminhtml/session')->setBlogData(false);

                        if ($this->getRequest()->getParam('back')) {
                            $this->_redirect('*/*/edit', array('id' => $model->getId()));
                            return;
                        }
                        $this->_redirect('*/*/');
                        return;
                    }
					catch (Exception $e) {
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
				if( $this->getRequest()->getParam('id') > 0 ) {
					try {
						$model = Mage::getModel('blog/category');
						$model->setId($this->getRequest()->getParam('id'))->delete();
						Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
						$this->_redirect('*/*/');
					} 
					catch (Exception $e) {
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
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
			$this->_redirect('*/*/');
		}
			 
		/**
		 *  Export order grid to Excel XML format
		 */
		public function exportExcelAction()
		{
			$fileName   = 'blog.xml';
			$grid       = $this->getLayout()->createBlock('blog/adminhtml_blog_grid');
			$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
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

    public function getCategoryList() {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $categoryTable = $resource->getTableName('blog_category');
        $select = $read->select()
            ->from($categoryTable, array('cat_id', 'name', 'is_active'))
            ->where('parent_id IS NULL');
        $categories = $read->fetchAll($select);
        $category = array();
        $category [] = array('value' => 'NULL', 'label' => 'None');
        foreach ($categories as $cat) {
            if ($cat['is_active']==true) {
                $category[] = array('value' => $cat['cat_id'], 'label' => $cat['name']);
                $child_cat = $this->getChildCat($cat['cat_id'], $category);
                $category = $child_cat;
            }
        }
        return $category;
    }

    public function getChildCat( $parent_cat_id, $category, $i='--') {
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $categoryTable = $resource->getTableName('blog_category');
        $select = $read->select()
            ->from($categoryTable, array('cat_id', 'name', 'is_active'))
            ->where('parent_id=?', $parent_cat_id);
        $categories = $read->fetchAll($select);
        if (isset ($categories)) {
            foreach ($categories as $cat) {
                if ($cat['is_active']==true) {
                    $category[] = array('value' => $cat['cat_id'], 'label' => $i.$cat['name']);
                    $child_cat = $this->getChildCat($cat['cat_id'], $category, $i.'--');
                    $category = $child_cat;

                }
            }
        }
        return $category;
    }
}