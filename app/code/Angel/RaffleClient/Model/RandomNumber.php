<?php


namespace Angel\RaffleClient\Model;

use Angel\RaffleClient\Api\Data\RandomNumberInterface;

class RandomNumber extends \Magento\Framework\Model\AbstractModel implements RandomNumberInterface
{

    protected $_eventPrefix = 'angel_raffleclient_randomnumber';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Angel\RaffleClient\Model\ResourceModel\RandomNumber::class);
    }

    /**
     * Get randomnumber_id
     * @return string
     */
    public function getRandomnumberId()
    {
        return $this->getData(self::RANDOMNUMBER_ID);
    }

    /**
     * Set randomnumber_id
     * @param string $randomnumberId
     * @return \Angel\RaffleClient\Api\Data\RandomNumberInterface
     */
    public function setRandomnumberId($randomnumberId)
    {
        return $this->setData(self::RANDOMNUMBER_ID, $randomnumberId);
    }

    /**
     * Get prize_id
     * @return string
     */
    public function getPrizeId()
    {
        return $this->getData(self::PRIZE_ID);
    }

    /**
     * Set prize_id
     * @param string $prizeId
     * @return \Angel\RaffleClient\Api\Data\RandomNumberInterface
     */
    public function setPrizeId($prizeId)
    {
        return $this->setData(self::PRIZE_ID, $prizeId);
    }

    /**
     * Get number
     * @return string
     */
    public function getNumber()
    {
        return $this->getData(self::NUMBER);
    }

    /**
     * Set number
     * @param string $number
     * @return \Angel\RaffleClient\Api\Data\RandomNumberInterface
     */
    public function setNumber($number)
    {
        return $this->setData(self::NUMBER, $number);
    }
}
