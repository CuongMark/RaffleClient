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
    const PAID = 3;

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

    /**
     * @var RandomNumberGenerate
     */
    protected $randomNumberGenerateModel;

    /**
     * @var \Angel\RaffleClient\Model\RandomNumberFactory
     */
    protected $randomNumberFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Order\Invoice\ItemFactory $invoiceItemFactory,
        \Angel\RaffleClient\Model\Raffle $raffle,
        \Angel\RaffleClient\Model\PrizeFactory $prizeFactory,
        \Angel\RaffleClient\Model\RandomNumberFactory $randomNumberFactory,
        \Angel\RaffleClient\Model\RandomNumberGenerate $numberGenerate,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ){
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->invoiceItemFactory = $invoiceItemFactory;
        $this->raffle = $raffle;
        $this->prizeFactory = $prizeFactory;
        $this->randomNumberGenerateModel = $numberGenerate;
        $this->randomNumberFactory = $randomNumberFactory;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Angel\RaffleClient\Model\ResourceModel\Ticket::class);
    }

    /**
     * create and check Random Number
     */
    public function check1(){
        if (!$this->getRaffle()->isAbleToGenerateByTicket()){
            return;
        }
        /** rest total prizes */
        $qty = $this->getRaffle()->getTotalPrizes() - $this->getRaffle()->getTotalRNGs();
        /** random number from start */
        $start = $this->getRaffle()->getCurrentLargestTicketNumber() + 1;
        $end = $this->getRaffle()->getTotalTicket() -1;
        if ($start > $end){
            return;
        }

        $RNGs = $this->randomNumberGenerateModel->randomNumberArrayGenerate($start, $end, $qty);
        $prizes = $this->getRaffle()->getPrizes();

        $i = 0;
        /** @var \Angel\RaffleClient\Model\Prize $_prize */
        foreach ($prizes as $_prize){
            $prizeQty = $_prize->getTotalRandomNumberNeedToGenerate();
            if ($prizeQty > 0){
                $next = $i + $prizeQty;
                for ($i; $i < $next; $i++){
                    if (isset($RNGs[$i]) && $this->getStart() <= $RNGs[$i] && $RNGs >= $this->getEnd()){
                        /** @var \Angel\RaffleClient\Model\RandomNumber $randomNumberModel */
                        $randomNumberModel = $this->randomNumberFactory->create();
                        $randomNumberModel->setPrizeId($_prize->getId())
                            ->setNumber($RNGs[$i]);
                        $randomNumberModel->getResource()->save($randomNumberModel);
                    }
                }
            }
        }
    }

    /**
     * create and check Random Number
     */
    public function check(){
        if (!$this->getRaffle()->isAbleToGenerateByTicket() || $this->getStatus() == static::CHECKED){
            return $this;
        }
        /** random number from start */
        $start = $this->getRaffle()->getCurrentLargestTicketNumber() + 1;
        $end = $this->getRaffle()->getTotalTicket() -1;
        if ($start > $end){
            $this->setStatus(static::CHECKED)->save();
            return $this;
        }
        $prizes = $this->getRaffle()->getPrizes();
        $exitRand = [];
        /** @var \Angel\RaffleClient\Model\Prize $_prize */
        try {
            foreach ($prizes as $_prize) {
                $prizeQty = $_prize->getTotalRandomNumberNeedToGenerate();
                if ($prizeQty>0){
                    for ($i = 0; $i < $prizeQty; $i++) {
                        $rand = $this->randomNumberGenerateModel->generateRand($start, $end, $exitRand);
                        if ($rand !== false && $this->getStart() <= $rand && $rand <= $this->getEnd()) {
                            /** @var \Angel\RaffleClient\Model\RandomNumber $randomNumberModel */
                            $randomNumberModel = $this->randomNumberFactory->create();
                            $randomNumberModel->setPrizeId($_prize->getId())
                                ->setNumber($rand);
                            $randomNumberModel->getResource()->save($randomNumberModel);
                        }
                    }
                }
            }
            $this->setStatus(static::CHECKED)->save();
        } catch (\Exception $e){

        }
        return $this;
    }

    /**
     * @param int $end
     * @param array $prizes
     * @return array
     */
    public function checkTest($end, &$prizes){
        $exitRand = [];
        $result = [];
        /** @var \Angel\RaffleClient\Model\Prize $_prize */
        try {
            foreach ($prizes as $key => $prizeQty) {
                if ($prizeQty>0){
                    $countNew = 0;
                    for ($i = 0; $i < $prizeQty; $i++) {
                        $rand = $this->randomNumberGenerateModel->generateRand($this->getStart(), $end, $exitRand);
                        if ($rand !== false && $this->getStart() <= $rand && $rand <= $this->getEnd()) {
                            /** @var \Angel\RaffleClient\Model\RandomNumber $randomNumberModel */
                            $result[] = $rand;
                            $countNew++;
                        }
                    }
                    $prizes[$key] -= $countNew;
                }
            }
        } catch (\Exception $e){

        }
        return $result;
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

    /**
     * @return Raffle
     */
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
     * @return int
     */
    public function getStart()
    {
        return $this->getData(self::START);
    }

    /**
     * Set start
     * @param int $start
     * @return \Angel\RaffleClient\Api\Data\TicketInterface
     */
    public function setStart($start)
    {
        return $this->setData(self::START, $start);
    }

    /**
     * Get end
     * @return int
     */
    public function getEnd()
    {
        return $this->getData(self::END);
    }

    /**
     * Set end
     * @param int $end
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
