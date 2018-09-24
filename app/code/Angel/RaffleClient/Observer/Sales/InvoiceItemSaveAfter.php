<?php


namespace Angel\RaffleClient\Observer\Sales;

class InvoiceItemSaveAfter implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Angel\RaffleClient\Model\Raffle
     */
    protected $raffle;

    /**
     * @var \Angel\RaffleClient\Service\Email
     */
    protected $email;

    public function __construct(
        \Angel\RaffleClient\Model\Raffle $raffle,
        \Angel\RaffleClient\Service\Email $email
    ){
        $this->raffle = $raffle;
        $this->email = $email;
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
        $invoice = $invoiceItem->getInvoice();
        $ticket = $this->raffle->createTicket($invoiceItem);
        if ($ticket){
            $ticket->check();
            $newTickets = $invoice->getData('newTickets')?$invoice->getData('newTickets'):[];
            $newTickets[] = $ticket;
            $invoice->setData('newTickets', $newTickets);
        }
        $invoiceItems = $invoice->getAllItems();
        $lastItem = end($invoiceItems);
        if ($lastItem && $invoiceItem->getid() == $lastItem->getId() && $invoice->getData('newTickets')){
            $this->email->sendWinningEmail($invoice->getData('newTickets'), $invoice->getOrder());
        }
    }
}
