<?php


namespace Angel\RaffleClient\Api\Data;

interface TestInterface
{

    const TOTAL_NUMBERS = 'total_numbers';
    const TOTAL_RAFFLES = 'total_raffles';
    const TOTAL = 'Total';
    const TEST_ID = 'test_id';

    /**
     * Get test_id
     * @return string|null
     */
    public function getTestId();

    /**
     * Set test_id
     * @param string $testId
     * @return \Angel\RaffleClient\Api\Data\TestInterface
     */
    public function setTestId($testId);

    /**
     * Get Total
     * @return string|null
     */
    public function getTotal();

    /**
     * Set Total
     * @param string $total
     * @return \Angel\RaffleClient\Api\Data\TestInterface
     */
    public function setTotal($total);

    /**
     * Get total_numbers
     * @return string|null
     */
    public function getTotalNumbers();

    /**
     * Set total_numbers
     * @param string $totalNumbers
     * @return \Angel\RaffleClient\Api\Data\TestInterface
     */
    public function setTotalNumbers($totalNumbers);

    /**
     * Get total_raffles
     * @return string|null
     */
    public function getTotalRaffles();

    /**
     * Set total_raffles
     * @param string $totalRaffles
     * @return \Angel\RaffleClient\Api\Data\TestInterface
     */
    public function setTotalRaffles($totalRaffles);
}
