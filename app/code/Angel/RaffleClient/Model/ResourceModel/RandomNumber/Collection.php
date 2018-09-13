<?php


namespace Angel\RaffleClient\Model\ResourceModel\RandomNumber;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    const PRIZE_TABLE = 'prize';
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Angel\RaffleClient\Model\RandomNumber::class,
            \Angel\RaffleClient\Model\ResourceModel\RandomNumber::class
        );
    }

    /**
     * @param $productId
     * @return int
     */
    public function getTotalRNG()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $idsSelect->reset(\Magento\Framework\DB\Select::COLUMNS);

        $idsSelect->columns('COUNT(main_table.randomnumber_id)', 'main_table');
        $result = $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
        if (isset($result[0]) && $result[0]){
            return $result[0];
        }
        return 0;
    }

    /**
     * @return $this
     */
    public function addProductIdToFilter($productId){
        $this->getSelect()->joinLeft(
            [static::PRIZE_TABLE => $this->getTable('angel_raffleclient_prize')],
            static::PRIZE_TABLE.'.prize_id = main_table.prize_id',
            ['product_id' => static::PRIZE_TABLE.'.product_id', 'prize_name' => static::PRIZE_TABLE.'.label']
        )->where(static::PRIZE_TABLE.'.product_id = ?', $productId);
        return $this;
    }
}
