<?php


namespace Angel\RaffleClient\Api\Data;

interface TransactionInterface
{

    const TRANSACTION_ID = 'transaction_id';
    const TICKET_ID = 'ticket_id';
    const PRICE = 'price';
    const CREATED_AT = 'created_at';
    const CODE = 'code';

    /**
     * Get transaction_id
     * @return string|null
     */
    public function getTransactionId();

    /**
     * Set transaction_id
     * @param string $transactionId
     * @return \Angel\RaffleClient\Api\Data\TransactionInterface
     */
    public function setTransactionId($transactionId);

    /**
     * Get ticket_id
     * @return string|null
     */
    public function getTicketId();

    /**
     * Set ticket_id
     * @param string $ticketId
     * @return \Angel\RaffleClient\Api\Data\TransactionInterface
     */
    public function setTicketId($ticketId);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Angel\RaffleClient\Api\Data\TransactionInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get code
     * @return string|null
     */
    public function getCode();

    /**
     * Set code
     * @param string $code
     * @return \Angel\RaffleClient\Api\Data\TransactionInterface
     */
    public function setCode($code);

    /**
     * Get price
     * @return string|null
     */
    public function getPrice();

    /**
     * Set price
     * @param string $price
     * @return \Angel\RaffleClient\Api\Data\TransactionInterface
     */
    public function setPrice($price);
}
