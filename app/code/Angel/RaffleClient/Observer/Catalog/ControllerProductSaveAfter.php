<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Angel\RaffleClient\Observer\Catalog;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class ControllerProductSaveAfter implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Angel\RaffleClient\Model\PrizeFactory
     */
    protected $prizeFactory;

    /**
     * @var \Angel\RaffleClient\Model\RaffleFactory
     */
    protected $raffleFactory;

    /**
     * ControllerProductSaveAfter constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Angel\RaffleClient\Model\PrizeFactory $prizeFactory,
        \Angel\RaffleClient\Model\RaffleFactory $raffleFactory
    )
    {
        $this->prizeFactory = $prizeFactory;
        $this->_request = $request;
        $this->raffleFactory = $raffleFactory;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getEvent()->getProduct();
        if (!in_array($product->getTypeId(), \Angel\RaffleClient\Model\Raffle::getRaffleProductTypes())){
            return $product;
        }
        $prizes = $product->getData('prizes');
        $existPrize = [];
        if (is_array($prizes)){
            foreach ($prizes as $_prize){
                /** @var \Angel\RaffleClient\Model\Prize $prize */
                $prize = $this->prizeFactory->create();
                if (isset($_prize['prize_id'])&&is_numeric($_prize['prize_id'])){
                    $prize->load($_prize['prize_id']);
                }
//                TODO check status before save
                $prize->setData('product_id', $product->getId());
                $prize->setData('label', $_prize['label']);
                $prize->setData('price', $_prize['price']);
                $prize->setData('price_type', $_prize['price_type']);
                $prize->setData('total', $_prize['total']);
                $prize->setData('sort_order', $_prize['sort_order']);
                $prize->save();
                $existPrize[] = $prize->getId();
            }
        }

        $prizesCollection = $this->raffleFactory->create()->setProduct($product)->getPrizes();
        foreach ($prizesCollection as $prize){
            if (!in_array($prize->getId(), $existPrize)){
                $prize->delete();
            }
        }

        if ($product->getTypeId() == \Angel\RaffleClient\Model\Fifty::TYPE_ID){
            $this->raffleFactory->create()->setProduct($product)->generateFiftyRaffleTicket();
        }
        return $this;
    }
}