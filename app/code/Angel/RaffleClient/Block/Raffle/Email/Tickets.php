<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Angel\RaffleClient\Block\Raffle\Email;

use \Magento\Framework\App\ObjectManager;
use \Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;

/**
 * Sales order history block
 *
 * @api
 * @since 100.0.2
 */
class Tickets extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'raffle/email/tickets.phtml';

    protected $_type = \Angel\RaffleClient\Model\Raffle::TYPE_ID;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @var \Angel\RaffleClient\Model\ResourceModel\Ticket\Collection
     */
    protected $tickets;

    /**
     * @var \Angel\RaffleClient\Model\ResourceModel\Ticket\CollectionFactory
     */
    protected $_ticketCollectionFactory;


    /**
     * Tickets constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Angel\RaffleClient\Model\ResourceModel\Ticket\CollectionFactory $ticketCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Angel\RaffleClient\Model\ResourceModel\Ticket\CollectionFactory $ticketCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        array $data = []
    ) {
        $this->_ticketCollectionFactory = $ticketCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_orderConfig = $orderConfig;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Tickets'));
    }


    /**
     * @return \Angel\RaffleClient\Model\ResourceModel\Ticket\Collection|bool
     */
    public function getTickets()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->tickets) {
            $this->tickets = $this->_ticketCollectionFactory->create();
            $this->tickets->addCustomerFilter($this->_customerSession->getCustomerId());
            $this->tickets->joinPrizes();
            $this->tickets->joinProductTypeId($this->_type);
            $this->tickets->addFieldToFilter('main_table.status', ['neq' => \Angel\RaffleClient\Model\Ticket::TRASH]);
        }
        return $this->tickets;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getTickets()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'sales.order.history.pager'
            )->setCollection(
                $this->getTickets()
            );
            $this->setChild('pager', $pager);
            $this->getTickets()->load();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param object $ticket
     * @return string
     */
    public function getViewUrl($ticket)
    {
        return $this->getUrl('raffle/index/ticket', ['ticket_id' => $ticket->getId()]);
    }

    /**
     * @param object $ticket
     * @return string
     */
    public function getTrashUrl($ticket)
    {
        return $this->getUrl('raffle/index/trash', ['ticket_id' => $ticket->getId()]);
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
}
