<?php

class CosmoCommerce_Bankcomm_Model_Log extends Mage_Core_Model_Abstract
{
    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('bankcomm/log');
    }
    public function getYes()
    {
        return 'yes';
    }
}