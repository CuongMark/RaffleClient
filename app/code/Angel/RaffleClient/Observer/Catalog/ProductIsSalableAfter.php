<?php


namespace Angel\RaffleClient\Observer\Catalog;

class ProductIsSalableAfter implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    protected $raffleFactory;
    function __construct(
        \Angel\RaffleClient\Model\RaffleFactory $raffleFactory
    ){
        $this->raffleFactory = $raffleFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magento\Framework\Event\Observer
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        if (!$observer->getSalable()->getIsSalable()){
            return $observer;
        }
        $salable = $observer->getSalable();
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $salable->getProduct();
        if ($product->getId() && $product->getTypeId() == \Angel\RaffleClient\Model\Fifty::TYPE_ID){
            $isSaleAbleFiftyTicket = $this->raffleFactory->create()->setProduct($product)->isSaleAbleFiftyTicket();
            if (!$isSaleAbleFiftyTicket){
                $observer->getSalable()->setIsSalable(false);
            }
        }
        return $observer;
    }
}
