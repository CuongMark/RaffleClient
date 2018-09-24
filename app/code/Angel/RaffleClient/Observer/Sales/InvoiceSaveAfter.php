<?php


namespace Angel\RaffleClient\Observer\Sales;

class InvoiceSaveAfter implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
//        $invoice = $observer->getData('invoice');
//        \Zend_Debug::dump(array_keys($invoice->getData()));
//        die('asdfasdfasd');
        //Your observer code
    }
}
