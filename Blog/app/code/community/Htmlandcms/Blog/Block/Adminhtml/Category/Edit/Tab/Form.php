<?php
class Htmlandcms_Blog_Block_Adminhtml_Category_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('blog_form', array('legend'=>Mage::helper('blog')->__('Item information')));
        $meta = $form->addFieldset('blog', array('legend'=>Mage::helper('blog')->__('Meta data')));

        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array('add_variables' => false,
            'add_widgets' => false,
            'add_images' => true,
            'files_browser_window_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index'),
            'files_browser_window_width' => (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_width'),
            'files_browser_window_height'=> (int) Mage::getConfig()->getNode('adminhtml/cms/browser/window_height')
        ));

        $fieldset->addField('image', 'image', array(
            'label' => Mage::helper('blog')->__('Image'),
            'required'  => false,
            'name' => 'image',
            'required'  => true,
        ));

        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('blog')->__('Category name'),
            'name' => 'name',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name' => 'stores[]',
                'label' => Mage::helper('blog')->__('Store View'),
                'title' => Mage::helper('blog')->__('Store View'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')
                        ->getStoreValuesForForm(false, true),
            ));
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name' => 'stores[]',
                'value' => Mage::app()->getStore(true)->getId()
            ));
        }

        $fieldset->addField('url', 'text', array(
            'label' => Mage::helper('blog')->__('URL Key'),
            'name' => 'url',
            'required'  => true,
        ));

        $fieldset->addField('is_active', 'select', array(
            'label' => Mage::helper('blog')->__('Is Active'),
            'name' => 'is_active',
            'values' => array (
                array(
                    'value'     => false,
                    'label'     => Mage::helper('blog')->__('Inactive'),
                ),

                array(
                    'value'     => true,
                    'label'     => Mage::helper('blog')->__('Active'),
                ),
            ),
            'required'  => false,
        ));

        $fieldset->addField('parent_id', 'select', array(
            'label' => Mage::helper('blog')->__('Parent category'),
            'name' => 'parent_id',
            'values' => Mage::registry('cat_list'),
        ));

        $fieldset->addField('body', 'editor', array(
            'label' => Mage::helper('blog')->__('Body'),
            'name' => 'body',
            'style' => 'height:12em;width:500px;',
            'wysiwyg' => true,
            'config' => $wysiwygConfig,
            'required'  => false,
        ));

        $meta->addField('meta_title', 'textarea', array(
            'label' => Mage::helper('blog')->__('Meta title'),
            'name' => 'meta_title',
            'style' => 'height:12em;width:500px;',
            'required'  => false,
        ));

        $meta->addField('description', 'textarea', array(
            'label' => Mage::helper('blog')->__('Description'),
            'name' => 'description',
            'style' => 'height:12em;width:500px;',
            'required'  => false,
        ));

        $meta->addField('keywords', 'textarea', array(
            'label' => Mage::helper('blog')->__('Keywords'),
            'name' => 'keywords',
            'style' => 'height:12em;width:500px;',
            'required'  => false,
        ));

        if (Mage::getSingleton('adminhtml/session')->getCategoryData())
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getCategoryData());
            Mage::getSingleton('adminhtml/session')->setCategoryData(null);
        }
        elseif(Mage::registry('category_data')) {
            $data = Mage::registry('category_data')->getData();

            $data['image'] = ($data['image']) ? 'image_blog/' . $data['image'] : null;
            $data['url'] = Mage::registry('rewrite_url');
            $form->setValues($data);
        }
        return parent::_prepareForm();
    }
}