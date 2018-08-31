<?php


namespace Angel\RaffleClient\Model;

use Angel\RaffleClient\Api\Data\PrizeInterface;

class Prize extends \Magento\Framework\Model\AbstractModel implements PrizeInterface
{

    protected $_eventPrefix = 'angel_raffleclient_prize';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Angel\RaffleClient\Model\ResourceModel\Prize::class);
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
     * @return \Angel\RaffleClient\Api\Data\PrizeInterface
     */
    public function setPrizeId($prizeId)
    {
        return $this->setData(self::PRIZE_ID, $prizeId);
    }

    /**
     * Get product_id
     * @return string
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * Set product_id
     * @param string $productId
     * @return \Angel\RaffleClient\Api\Data\PrizeInterface
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Get total
     * @return string
     */
    public function getTotal()
    {
        return $this->getData(self::TOTAL);
    }

    /**
     * Set total
     * @param string $total
     * @return \Angel\RaffleClient\Api\Data\PrizeInterface
     */
    public function setTotal($total)
    {
        return $this->setData(self::TOTAL, $total);
    }

    /**
     * Get label
     * @return string
     */
    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    /**
     * Set label
     * @param string $label
     * @return \Angel\RaffleClient\Api\Data\PrizeInterface
     */
    public function setLabel($label)
    {
        return $this->setData(self::LABEL, $label);
    }

    /**
     * Get price
     * @return string
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    /**
     * Set price
     * @param string $price
     * @return \Angel\RaffleClient\Api\Data\PrizeInterface
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * Get status
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Set status
     * @param string $status
     * @return \Angel\RaffleClient\Api\Data\PrizeInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get price_type
     * @return string
     */
    public function getPriceType()
    {
        return $this->getData(self::PRICE_TYPE);
    }

    /**
     * Set price_type
     * @param string $priceType
     * @return \Angel\RaffleClient\Api\Data\PrizeInterface
     */
    public function setPriceType($priceType)
    {
        return $this->setData(self::PRICE_TYPE, $priceType);
    }

    /**
     * Get sort_order
     * @return string
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * Set sort_order
     * @param string $sortOrder
     * @return \Angel\RaffleClient\Api\Data\PrizeInterface
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }
}