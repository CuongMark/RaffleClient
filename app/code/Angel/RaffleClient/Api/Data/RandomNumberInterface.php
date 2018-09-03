<?php


namespace Angel\RaffleClient\Api\Data;

interface RandomNumberInterface
{

    const RANDOMNUMBER_ID = 'randomnumber_id';
    const NUMBER = 'number';
    const PRICE = 'price';
    const PRIZE_ID = 'prize_id';

    /**
     * Get randomnumber_id
     * @return string|null
     */
    public function getRandomnumberId();

    /**
     * Set randomnumber_id
     * @param string $randomnumberId
     * @return \Angel\RaffleClient\Api\Data\RandomNumberInterface
     */
    public function setRandomnumberId($randomnumberId);

    /**
     * Get prize_id
     * @return string|null
     */
    public function getPrizeId();

    /**
     * Set prize_id
     * @param string $prizeId
     * @return \Angel\RaffleClient\Api\Data\RandomNumberInterface
     */
    public function setPrizeId($prizeId);

    /**
     * Get number
     * @return string|null
     */
    public function getNumber();

    /**
     * Set number
     * @param string $number
     * @return \Angel\RaffleClient\Api\Data\RandomNumberInterface
     */
    public function setNumber($number);

    /**
     * Get price
     * @return string|null
     */
    public function getPrice();

    /**
     * Set price
     * @param string $price
     * @return \Angel\RaffleClient\Api\Data\RandomNumberInterface
     */
    public function setPrice($price);
}