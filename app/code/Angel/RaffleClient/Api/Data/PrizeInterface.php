<?php


namespace Angel\RaffleClient\Api\Data;

interface PrizeInterface
{

    const LABEL = 'label';
    const PRIZE_ID = 'prize_id';
    const STATUS = 'status';
    const PRICE = 'price';
    const TOTAL = 'total';
    const PRODUCT_ID = 'product_id';

    /**
     * Get prize_id
     * @return string|null
     */
    public function getPrizeId();

    /**
     * Set prize_id
     * @param string $prizeId
     * @return \Angel\RaffleClient\Api\Data\PrizeInterface
     */
    public function setPrizeId($prizeId);

    /**
     * Get product_id
     * @return string|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param string $productId
     * @return \Angel\RaffleClient\Api\Data\PrizeInterface
     */
    public function setProductId($productId);

    /**
     * Get total
     * @return string|null
     */
    public function getTotal();

    /**
     * Set total
     * @param string $total
     * @return \Angel\RaffleClient\Api\Data\PrizeInterface
     */
    public function setTotal($total);

    /**
     * Get label
     * @return string|null
     */
    public function getLabel();

    /**
     * Set label
     * @param string $label
     * @return \Angel\RaffleClient\Api\Data\PrizeInterface
     */
    public function setLabel($label);

    /**
     * Get price
     * @return string|null
     */
    public function getPrice();

    /**
     * Set price
     * @param string $price
     * @return \Angel\RaffleClient\Api\Data\PrizeInterface
     */
    public function setPrice($price);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Angel\RaffleClient\Api\Data\PrizeInterface
     */
    public function setStatus($status);
}
