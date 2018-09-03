<?php


namespace Angel\RaffleClient\Observer\Sales;

class InvoiceItemSaveAfter implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Angel\RaffleClient\Model\Raffle
     */
    protected $raffle;

    public function __construct(
        \Angel\RaffleClient\Model\Raffle $raffle
    ){
        $this->raffle = $raffle;
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
        /** @var \Magento\Sales\Model\Order\Invoice\Item $invoiceItem */
        $invoiceItem = $observer->getData('invoice_item');
        $ticket = $this->raffle->createTicket($invoiceItem);
        if ($ticket){
            $ticket->check();
        }
    }
}
