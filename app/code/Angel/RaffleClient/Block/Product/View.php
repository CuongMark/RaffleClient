<?php


namespace Angel\RaffleClient\Block\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;

class View extends \Magento\Catalog\Block\Product\View
{

    public function isRaffleFiftyProduct(){
        return $this->getProduct()->getTypeId() == \Angel\RaffleClient\Model\Fifty::TYPE_ID;
    }

    public function getRaffleTimeLeft(){
        $date = new \DateTime();
        $raffleEnd = new \DateTime($this->getProduct()->getRaffleEnd());
        return $raffleEnd->getTimestamp() - $date->getTimestamp();
    }
}
