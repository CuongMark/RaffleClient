<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Angel\RaffleClient\Block\Raffle;

use \Magento\Framework\App\ObjectManager;
use \Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;

/**
 * Sales order history block
 *
 * @api
 * @since 100.0.2
 */
class Ticket extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'index/ticket.phtml';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Angel\RaffleClient\Model\ResourceModel\Ticket\Collection
     */
    protected $ticket;

    /**
     * @var \Angel\RaffleClient\Model\TicketFactory
     */
    protected $_ticketFactory;


    /**
     * Tickets constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Angel\RaffleClient\Model\TicketFactory $ticketFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Angel\RaffleClient\Model\TicketFactory $ticketFactory,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->_ticketFactory = $ticketFactory;
        $this->_customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Ticket Information'));
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getWonNumbers()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'sales.order.history.pager'
            )->setCollection(
                $this->getWonNumbers()
            );
            $this->setChild('pager', $pager);
            $this->getWonNumbers()->load();
        }
        return $this;
    }

    public function getWonNumbers(){
        return $this->getTicket()->getWinningNumberCollection();
    }

    /**
     * @return \Angel\RaffleClient\Model\ticket|null
     */
    public function getTicket(){
        $ticketId = $this->getRequest()->getParam('ticket_id');
        if ($ticketId){
            $ticket = $this->_ticketFactory->create()->load($ticketId);
            if ($ticket->getId()
//                && $this->_customerSession->getCustomerId() == $ticket->getCustomerId()
            ){
                return $ticket;
            }
        }
        return null;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

}
