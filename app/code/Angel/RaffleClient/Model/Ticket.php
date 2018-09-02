<?php


namespace Angel\RaffleClient\Model;

use Angel\RaffleClient\Api\Data\TicketInterface;
use Magento\Sales\Model\Order\Invoice\Item;
use Magento\Sales\Model\Order\Invoice\ItemFactory;

class Ticket extends \Magento\Framework\Model\AbstractModel implements TicketInterface
{

    const CHECKED = 1;
    const NOT_CHECKED = 0;
    const WAIT = 2;

    protected $_eventPrefix = 'angel_raffleclient_ticket';

    /**
     * @var ItemFactory
     */
    protected $invoiceItemFactory;

    /**
     * @var PrizeFactory
     */
    protected $prizeFactory;

    /**
     * @var Raffle
     */
    protected $raffle;

    /**
     * @var \Magento\Sales\Model\Order\Invoice\Item
     */
    protected $invoiceItem;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \Magento\Sales\Model\Order\Invoice\ItemFactory $invoiceItemFactory,
        \Angel\RaffleClient\Model\Raffle $raffle,
        \Angel\RaffleClient\Model\PrizeFactory $prizeFactory,
        array $data = [])
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->invoiceItemFactory = $invoiceItemFactory;
        $this->raffle = $raffle;
        $this->prizeFactory = $prizeFactory;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Angel\RaffleClient\Model\ResourceModel\Ticket::class);
    }

    public function check(array $RNGs){

    }

    public function validate(){

    }

    /**
     * \Magento\Sales\Model\Order\Invoice\Item
     * @return Item
     */
    public function getInvoiceItem(){
        if (!$this->invoiceItem){
            $this->invoiceItem = $this->invoiceItemFactory->create()->load($this->getInvoiceItemId());
        }
        return $this->invoiceItem;
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice\Item $invoiceItem
     * @return $this
     */
    public function setInvoiceItem($invoiceItem){
        $this->invoiceItem = $invoiceItem;
        return $this;
    }

    public function getRaffle(){
        if (!$this->raffle){
            $this->raffle = $this->raffle->setProduct($this->getInvoiceItem()->getProductId());
        }
        return $this->raffle;
    }

    /**
     * Get ticket_id
     * @return string
     */
    public function getTicketId()
    {
        return $this->getData(self::TICKET_ID);
    }

    /**
     * Set ticket_id
     * @param string $ticketId
     * @return \Angel\RaffleClient\Api\Data\TicketInterface
     */
    public function setTicketId($ticketId)
    {
        return $this->setData(self::TICKET_ID, $ticketId);
    }

    /**
     * Get invoice_item_id
     * @return string
     */
    public function getInvoiceItemId()
    {
        return $this->getData(self::INVOICE_ITEM_ID);
    }

    /**
     * Set invoice_item_id
     * @param string $invoiceItemId
     * @return \Angel\RaffleClient\Api\Data\TicketInterface
     */
    public function setInvoiceItemId($invoiceItemId)
    {
        return $this->setData(self::INVOICE_ITEM_ID, $invoiceItemId);
    }

    /**
     * Get start
     * @return string
     */
    public function getStart()
    {
        return $this->getData(self::START);
    }

    /**
     * Set start
     * @param string $start
     * @return \Angel\RaffleClient\Api\Data\TicketInterface
     */
    public function setStart($start)
    {
        return $this->setData(self::START, $start);
    }

    /**
     * Get end
     * @return string
     */
    public function getEnd()
    {
        return $this->getData(self::END);
    }

    /**
     * Set end
     * @param string $end
     * @return \Angel\RaffleClient\Api\Data\TicketInterface
     */
    public function setEnd($end)
    {
        return $this->setData(self::END, $end);
    }

    /**
     * Get serial
     * @return string
     */
    public function getSerial()
    {
        return $this->getData(self::SERIAL);
    }

    /**
     * Set serial
     * @param string $serial
     * @return \Angel\RaffleClient\Api\Data\TicketInterface
     */
    public function setSerial($serial)
    {
        return $this->setData(self::SERIAL, $serial);
    }

    /**
     * Get status
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Set status
     * @param string $status
     * @return \Angel\RaffleClient\Api\Data\TicketInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }
}
