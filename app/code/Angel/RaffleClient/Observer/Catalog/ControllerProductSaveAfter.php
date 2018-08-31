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
     * ControllerProductSaveAfter constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Angel\RaffleClient\Model\PrizeFactory $prizeFactory
    )
    {
        $this->prizeFactory = $prizeFactory;
        $this->_request = $request;
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
        $prizes = $product->getData('prizes');
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
            }
        }
        return $this;
    }
}