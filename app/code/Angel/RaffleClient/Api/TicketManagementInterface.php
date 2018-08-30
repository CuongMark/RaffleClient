<?php


namespace Angel\RaffleClient\Api;

interface TicketManagementInterface
{

    /**
     * GET for Ticket api
     * @param string $param
     * @return string
     */
    public function getTicket($param);
}
