<?php


namespace Angel\RaffleClient\Model\ResourceModel\Ticket;

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
            \Angel\RaffleClient\Model\Ticket::class,
            \Angel\RaffleClient\Model\ResourceModel\Ticket::class
        );
    }
}
