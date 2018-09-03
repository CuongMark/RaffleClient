<?php


namespace Angel\RaffleClient\Model\ResourceModel\Ticket;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    const INVOICE_ITEM_TABLE = 'invoice_item';
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

    /**
     * @param int $productId
     * @return int
     */
    public function getCurrentLargestTicketNumber($productId)
    {
        $this->joinInvoiceItemTable();
        $this->addFieldToFilter(static::INVOICE_ITEM_TABLE.'.product_id', $productId);

        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $idsSelect->reset(\Magento\Framework\DB\Select::COLUMNS);

        $idsSelect->columns('MAX(main_table.end)', 'main_table');
        $result = $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
        if (isset($result[0]) && $result[0]){
            return $result[0];
        }
        return -1;
    }

    /**
     * @return $this
     */
    protected function joinInvoiceItemTable(){
        $this->getSelect()->joinLeft(
            [static::INVOICE_ITEM_TABLE => $this->getTable('sales_invoice_item')],
            static::INVOICE_ITEM_TABLE.'.entity_id = main_table.invoice_item_id',
            ['product_id' => static::INVOICE_ITEM_TABLE.'.product_id']
        );
        return $this;
    }

    /**
     * @param int $productId
     * @return $this
     */
    public function getRaffleTickets($productId){
        $this->joinInvoiceItemTable();
        $this->addFieldToFilter(static::INVOICE_ITEM_TABLE.'.product_id', $productId);
        return $this;
    }
}
