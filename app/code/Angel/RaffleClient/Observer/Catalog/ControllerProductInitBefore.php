<?php


namespace Angel\RaffleClient\Observer\Catalog;

class ControllerProductInitBefore implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Angel\RaffleClient\Model\RaffleFactory
     */
    protected $raffleFactory;

    public function __construct(
        \Angel\RaffleClient\Model\RaffleFactory $raffleFactory
    ){
        $this->raffleFactory = $raffleFactory;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getEvent()->getProduct();
        if ($product->getTypeId() == \Angel\RaffleClient\Model\Fifty::TYPE_ID){
            $this->raffleFactory->create()->setProduct($product)->generateFiftyRaffleTicket();
        }
    }
}
