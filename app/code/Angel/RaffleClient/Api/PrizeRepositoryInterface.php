<?php


namespace Angel\RaffleClient\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface PrizeRepositoryInterface
{

    /**
     * Save Prize
     * @param \Angel\RaffleClient\Api\Data\PrizeInterface $prize
     * @return \Angel\RaffleClient\Api\Data\PrizeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Angel\RaffleClient\Api\Data\PrizeInterface $prize
    );

    /**
     * Retrieve Prize
     * @param string $prizeId
     * @return \Angel\RaffleClient\Api\Data\PrizeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($prizeId);

    /**
     * Retrieve Prize matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Angel\RaffleClient\Api\Data\PrizeSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Prize
     * @param \Angel\RaffleClient\Api\Data\PrizeInterface $prize
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Angel\RaffleClient\Api\Data\PrizeInterface $prize
    );

    /**
     * Delete Prize by ID
     * @param string $prizeId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($prizeId);
}
