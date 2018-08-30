<?php


namespace Angel\RaffleClient\Api\Data;

interface TicketSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Ticket list.
     * @return \Angel\RaffleClient\Api\Data\TicketInterface[]
     */
    public function getItems();

    /**
     * Set invoice_item_id list.
     * @param \Angel\RaffleClient\Api\Data\TicketInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
