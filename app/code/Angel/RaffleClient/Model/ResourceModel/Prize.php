<?php


namespace Angel\RaffleClient\Model\ResourceModel;

class Prize extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('angel_raffleclient_prize', 'prize_id');
    }
}
