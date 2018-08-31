<?php


namespace Angel\RaffleClient\Model;

class Raffle
{
    const RAFFLE = 'raffle';
    const FIFTY = 'fifty';

    static function getRaffleProductTypes(){
        return [static::RAFFLE, static::FIFTY];
    }
}
