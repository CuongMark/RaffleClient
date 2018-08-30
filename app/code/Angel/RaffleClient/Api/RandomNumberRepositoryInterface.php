<?php


namespace Angel\RaffleClient\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface RandomNumberRepositoryInterface
{

    /**
     * Save RandomNumber
     * @param \Angel\RaffleClient\Api\Data\RandomNumberInterface $randomNumber
     * @return \Angel\RaffleClient\Api\Data\RandomNumberInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Angel\RaffleClient\Api\Data\RandomNumberInterface $randomNumber
    );

    /**
     * Retrieve RandomNumber
     * @param string $randomnumberId
     * @return \Angel\RaffleClient\Api\Data\RandomNumberInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($randomnumberId);

    /**
     * Retrieve RandomNumber matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Angel\RaffleClient\Api\Data\RandomNumberSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete RandomNumber
     * @param \Angel\RaffleClient\Api\Data\RandomNumberInterface $randomNumber
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Angel\RaffleClient\Api\Data\RandomNumberInterface $randomNumber
    );

    /**
     * Delete RandomNumber by ID
     * @param string $randomnumberId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($randomnumberId);
}
