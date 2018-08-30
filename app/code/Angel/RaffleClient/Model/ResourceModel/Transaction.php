<?php


namespace Angel\RaffleClient\Model\ResourceModel;

class Transaction extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('angel_raffleclient_transaction', 'transaction_id');
    }
}
