<?php


namespace Angel\RaffleClient\Model;

use Angel\RaffleClient\Api\Data\TransactionInterface;

class Transaction extends \Magento\Framework\Model\AbstractModel implements TransactionInterface
{

    protected $_eventPrefix = 'angel_raffleclient_transaction';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Angel\RaffleClient\Model\ResourceModel\Transaction::class);
    }

    /**
     * Get transaction_id
     * @return string
     */
    public function getTransactionId()
    {
        return $this->getData(self::TRANSACTION_ID);
    }

    /**
     * Set transaction_id
     * @param string $transactionId
     * @return \Angel\RaffleClient\Api\Data\TransactionInterface
     */
    public function setTransactionId($transactionId)
    {
        return $this->setData(self::TRANSACTION_ID, $transactionId);
    }

    /**
     * Get ticket_id
     * @return string
     */
    public function getTicketId()
    {
        return $this->getData(self::TICKET_ID);
    }

    /**
     * Set ticket_id
     * @param string $ticketId
     * @return \Angel\RaffleClient\Api\Data\TransactionInterface
     */
    public function setTicketId($ticketId)
    {
        return $this->setData(self::TICKET_ID, $ticketId);
    }

    /**
     * Get created_at
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Angel\RaffleClient\Api\Data\TransactionInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get code
     * @return string
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * Set code
     * @param string $code
     * @return \Angel\RaffleClient\Api\Data\TransactionInterface
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
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
     * @return \Angel\RaffleClient\Api\Data\TransactionInterface
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }
}
