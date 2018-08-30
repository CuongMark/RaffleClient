<?php


namespace Angel\RaffleClient\Api;

interface RaffleManagementInterface
{

    /**
     * GET for Raffle api
     * @param string $param
     * @return string
     */
    public function getRaffle($param);
}
