<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Angel\RaffleClient\Block\Onepage;

class Success extends \Angel\RaffleClient\Block\Raffle\Tickets
{
    /**
     * @var string
     */
    protected $_template = 'raffle/orderTickets.phtml';
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Angel\RaffleClient\Model\ResourceModel\Ticket\CollectionFactory
     */
    protected $_ticketCollectionFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Angel\RaffleClient\Model\ResourceModel\Ticket\CollectionFactory $ticketCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    ){
        parent::__construct($context, $ticketCollectionFactory, $customerSession, $orderConfig, $data);
        $this->_checkoutSession = $checkoutSession;
    }

    public function getTickets(){
        /** @var \Angel\RaffleClient\Model\ResourceModel\Ticket\Collection $collection */
        $collection = $this->_ticketCollectionFactory->create();
        return $collection->joinPrizes()->joinInvoiceItemTable()->joinInvoiceTable()->joinOrderTable($this->_checkoutSession->getLastRealOrderId());
    }

    public function hasWinningTicket(){
        foreach ($this->getTickets() as $ticket){
            if ($ticket->getStatus() == \Angel\RaffleClient\Model\Ticket::WON)
                return true;
        }
        return false;
    }
}
