<?php


namespace Angel\RaffleClient\Model;

use Angel\RaffleClient\Api\Data\TestInterface;

class Test extends \Magento\Framework\Model\AbstractModel implements TestInterface
{

    protected $_eventPrefix = 'angel_raffleclient_test';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Angel\RaffleClient\Model\ResourceModel\Test::class);
    }

    /**
     * Get test_id
     * @return string
     */
    public function getTestId()
    {
        return $this->getData(self::TEST_ID);
    }

    /**
     * Set test_id
     * @param string $testId
     * @return \Angel\RaffleClient\Api\Data\TestInterface
     */
    public function setTestId($testId)
    {
        return $this->setData(self::TEST_ID, $testId);
    }

    /**
     * Get Total
     * @return string
     */
    public function getTotal()
    {
        return $this->getData(self::TOTAL);
    }

    /**
     * Set Total
     * @param string $total
     * @return \Angel\RaffleClient\Api\Data\TestInterface
     */
    public function setTotal($total)
    {
        return $this->setData(self::TOTAL, $total);
    }

    /**
     * Get total_numbers
     * @return string
     */
    public function getTotalNumbers()
    {
        return $this->getData(self::TOTAL_NUMBERS);
    }

    /**
     * Set total_numbers
     * @param string $totalNumbers
     * @return \Angel\RaffleClient\Api\Data\TestInterface
     */
    public function setTotalNumbers($totalNumbers)
    {
        return $this->setData(self::TOTAL_NUMBERS, $totalNumbers);
    }

    /**
     * Get total_raffles
     * @return string
     */
    public function getTotalRaffles()
    {
        return $this->getData(self::TOTAL_RAFFLES);
    }

    /**
     * Set total_raffles
     * @param string $totalRaffles
     * @return \Angel\RaffleClient\Api\Data\TestInterface
     */
    public function setTotalRaffles($totalRaffles)
    {
        return $this->setData(self::TOTAL_RAFFLES, $totalRaffles);
    }
}
