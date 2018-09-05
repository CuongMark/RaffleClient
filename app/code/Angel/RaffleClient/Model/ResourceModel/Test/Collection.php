<?php


namespace Angel\RaffleClient\Model\ResourceModel\Test;

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
            \Angel\RaffleClient\Model\Test::class,
            \Angel\RaffleClient\Model\ResourceModel\Test::class
        );
    }
}
