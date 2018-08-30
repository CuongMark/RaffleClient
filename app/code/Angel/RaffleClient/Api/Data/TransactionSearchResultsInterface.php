<?php


namespace Angel\RaffleClient\Api\Data;

interface TransactionSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Transaction list.
     * @return \Angel\RaffleClient\Api\Data\TransactionInterface[]
     */
    public function getItems();

    /**
     * Set ticket_id list.
     * @param \Angel\RaffleClient\Api\Data\TransactionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
