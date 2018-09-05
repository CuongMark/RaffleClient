<?php


namespace Angel\RaffleClient\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface TestRepositoryInterface
{

    /**
     * Save Test
     * @param \Angel\RaffleClient\Api\Data\TestInterface $test
     * @return \Angel\RaffleClient\Api\Data\TestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Angel\RaffleClient\Api\Data\TestInterface $test
    );

    /**
     * Retrieve Test
     * @param string $testId
     * @return \Angel\RaffleClient\Api\Data\TestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($testId);

    /**
     * Retrieve Test matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Angel\RaffleClient\Api\Data\TestSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Test
     * @param \Angel\RaffleClient\Api\Data\TestInterface $test
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Angel\RaffleClient\Api\Data\TestInterface $test
    );

    /**
     * Delete Test by ID
     * @param string $testId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($testId);
}
