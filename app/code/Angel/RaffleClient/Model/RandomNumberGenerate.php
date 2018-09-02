<?php


namespace Angel\RaffleClient\Model;

class RandomNumberGenerate
{
    public function __construct(){
    }

    public function randomNumberArrayGenerate($start, $end, $qty){
        if ($start <0 || $end <0 || $qty <= 0 || $end - $start < $qty)
            return null;
        $result = [];
        while ($qty <= count($result)){
            $randomNumber = rand($start, $end);
            if (!in_array($randomNumber, $result)){
                $result[] = $randomNumber;
            }
        }
        return $result;
    }
}
