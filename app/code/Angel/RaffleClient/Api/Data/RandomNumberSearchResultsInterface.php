<?php


namespace Angel\RaffleClient\Api\Data;

interface RandomNumberSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get RandomNumber list.
     * @return \Angel\RaffleClient\Api\Data\RandomNumberInterface[]
     */
    public function getItems();

    /**
     * Set prize_id list.
     * @param \Angel\RaffleClient\Api\Data\RandomNumberInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
