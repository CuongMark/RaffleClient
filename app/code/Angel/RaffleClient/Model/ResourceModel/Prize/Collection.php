<?php


namespace Angel\RaffleClient\Model\ResourceModel\Prize;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Angel\RaffleClient\Model\Prize::class,
            \Angel\RaffleClient\Model\ResourceModel\Prize::class
        );
    }
}
