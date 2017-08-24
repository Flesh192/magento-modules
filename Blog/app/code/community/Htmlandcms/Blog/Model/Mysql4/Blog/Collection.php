<?php
class Htmlandcms_Blog_Model_Mysql4_Blog_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    public function _construct(){
        $this->_init('blog/blog');
    }


    /**
     * Join fields
     *
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Resource_Customer_Totals_Collection
     */
    protected function _joinFields($from = '', $to = '')
    {
        $this->addFieldToFilter('created_at', array('from' => $from, 'to' => $to, 'datetime' => true));
        return $this;
    }

    /**
     * Set date range
     *
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Resource_Customer_Totals_Collection
     */
    public function setDateRange($from, $to)
    {
        $this->_reset()
            ->_joinFields($from, $to);
        return $this;
    }

    /**
     * Set store filter collection
     *
     * @param array $storeIds
     * @return Mage_Reports_Model_Resource_Customer_Totals_Collection
     */
    public function setStoreIds($storeIds)
    {
        if ($storeIds) {
            //$this->addFieldToFilter('store_id', array('in' => (array)$storeIds));
        }

        return $this;
    }

}