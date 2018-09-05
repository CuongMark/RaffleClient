<?php


namespace Angel\RaffleClient\Api\Data;

interface TestSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Test list.
     * @return \Angel\RaffleClient\Api\Data\TestInterface[]
     */
    public function getItems();

    /**
     * Set Total list.
     * @param \Angel\RaffleClient\Api\Data\TestInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
