<?php


namespace Angel\RaffleClient\Api\Data;

interface TicketInterface
{

    const SERIAL = 'serial';
    const START = 'start';
    const INVOICE_ITEM_ID = 'invoice_item_id';
    const STATUS = 'status';
    const TICKET_ID = 'ticket_id';
    const END = 'end';

    /**
     * Get ticket_id
     * @return string|null
     */
    public function getTicketId();

    /**
     * Set ticket_id
     * @param string $ticketId
     * @return \Angel\RaffleClient\Api\Data\TicketInterface
     */
    public function setTicketId($ticketId);

    /**
     * Get invoice_item_id
     * @return string|null
     */
    public function getInvoiceItemId();

    /**
     * Set invoice_item_id
     * @param string $invoiceItemId
     * @return \Angel\RaffleClient\Api\Data\TicketInterface
     */
    public function setInvoiceItemId($invoiceItemId);

    /**
     * Get start
     * @return string|null
     */
    public function getStart();

    /**
     * Set start
     * @param string $start
     * @return \Angel\RaffleClient\Api\Data\TicketInterface
     */
    public function setStart($start);

    /**
     * Get end
     * @return string|null
     */
    public function getEnd();

    /**
     * Set end
     * @param string $end
     * @return \Angel\RaffleClient\Api\Data\TicketInterface
     */
    public function setEnd($end);

    /**
     * Get serial
     * @return string|null
     */
    public function getSerial();

    /**
     * Set serial
     * @param string $serial
     * @return \Angel\RaffleClient\Api\Data\TicketInterface
     */
    public function setSerial($serial);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Angel\RaffleClient\Api\Data\TicketInterface
     */
    public function setStatus($status);
}
