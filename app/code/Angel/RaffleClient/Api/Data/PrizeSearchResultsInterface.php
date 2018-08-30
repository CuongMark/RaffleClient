<?php


namespace Angel\RaffleClient\Api\Data;

interface PrizeSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Prize list.
     * @return \Angel\RaffleClient\Api\Data\PrizeInterface[]
     */
    public function getItems();

    /**
     * Set product_id list.
     * @param \Angel\RaffleClient\Api\Data\PrizeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
