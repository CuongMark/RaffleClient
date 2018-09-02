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

    /**
     * @return int
     */
    public function getTotalPrizes()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $idsSelect->reset(\Magento\Framework\DB\Select::COLUMNS);

        $idsSelect->columns('SUM(main_table.total)', 'main_table');
        $result = $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
        if (isset($result[0]) && $result[0]){
            return $result[0];
        }
        return 0;
    }
}
