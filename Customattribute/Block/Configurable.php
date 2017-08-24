<?php

/**
 * Class Staubli_Customattribute_Block_Catalog_Product_View_Type_Configurable
 * load qty as custom attribute
 */
class Staubli_Customattribute_Block_Configurable
    extends Mage_Catalog_Block_Product_View_Type_Configurable
{

    public function getJsonConfig()
    {
        $config = parent::getJsonConfig();
        $config = Mage::helper('core')->jsonDecode($config);

        foreach ($config['attributes'] as $attid => $attinfo) {
            foreach ($attinfo['options'] as $key => $attoption) {
                // get stock value per product
                $stocks = array();
                foreach ($attoption['products'] as $prod) {
                    $_product = Mage::getModel('catalog/product')->load($prod);
                    $_qty = Mage::getModel('cataloginventory/stock_item')
                        ->loadByProduct($_product)->getQty();
                    $stocks[$prod] = (int)$_qty;
                }
                $config['attributes'][$attid]['options'][$key]['stock']
                    = $stocks;
            }
        }

        return Mage::helper('core')->jsonEncode($config);
    }
}
