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
        while ($qty > count($result)){
            $randomNumber = mt_rand($start, $end);
            if (!in_array($randomNumber, $result)){
                $result[] = $randomNumber;
            }
        }
        return $result;
    }

    /**
     * @param int $start
     * @param int $end
     * @param array $exitRand
     * @return bool|int
     */
    public function generateRand($start, $end, &$exitRand){
        if ($start == $end){
            $exitRand[] = $start;
            return $start;
        }
        if ($end - $start +1 <= count($exitRand))
            return false;
        $rand = mt_rand($start, $end);
        while (in_array($rand, $exitRand)){
            $rand = mt_rand($start, $end);
        }
        $exitRand[] = $rand;
        return $rand;
    }
}
