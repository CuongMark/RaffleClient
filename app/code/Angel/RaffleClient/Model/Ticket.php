<?php


namespace Angel\RaffleClient\Model;

use Angel\RaffleClient\Api\Data\TicketInterface;
use Magento\Sales\Model\Order\Invoice\Item;
use Magento\Sales\Model\Order\Invoice\ItemFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Ticket extends \Magento\Framework\Model\AbstractModel implements TicketInterface
{

    const CHECKED = 1;
    const NOT_CHECKED = 0;
    const WAIT = 2;
    const PAID = 3;
    const WON = 4;
    const LOSE = 5;
    const FALSE = 6;
    const TRASH = 7;

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
     * @var RaffleFactory
     */
    protected $raffleFactory;

    /**
     * @var Raffle
     */
    protected $raffle;

    /**
     * @var \Magento\Sales\Model\Order\Invoice\Item
     */
    protected $invoiceItem;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * @var \Angel\RaffleClient\Model\ResourceModel\RandomNumber\Collection
     */
    protected $randomNumberCollection;

    /**
     * @var RandomNumberGenerate
     */
    protected $randomNumberGenerateModel;

    /**
     * @var \Angel\RaffleClient\Model\RandomNumberFactory
     */
    protected $randomNumberFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * Ticket constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ItemFactory $invoiceItemFactory
     * @param RaffleFactory $raffleFactory
     * @param PrizeFactory $prizeFactory
     * @param RandomNumberFactory $randomNumberFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param RandomNumberGenerate $numberGenerate
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Order\Invoice\ItemFactory $invoiceItemFactory,
        \Angel\RaffleClient\Model\RaffleFactory $raffleFactory,
        \Angel\RaffleClient\Model\PrizeFactory $prizeFactory,
        \Angel\RaffleClient\Model\RandomNumberFactory $randomNumberFactory,
        \Angel\RaffleClient\Model\RandomNumberGenerate $numberGenerate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ){
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->invoiceItemFactory = $invoiceItemFactory;
        $this->raffleFactory = $raffleFactory;
        $this->prizeFactory = $prizeFactory;
        $this->randomNumberGenerateModel = $numberGenerate;
        $this->randomNumberFactory = $randomNumberFactory;
        $this->priceCurrency = $priceCurrency;
        $this->dateTime = $dateTime;
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
    public function check(){
        if (!$this->getRaffle()->isAbleToGenerateByTicket() || $this->getStatus() != static::NOT_CHECKED){
            return $this;
        }
        /** random number from start */
        $start = $this->getStart();
        $end = $this->getRaffle()->getTotalTicket();
        if ($start > $end){
            $this->setStatus(static::FALSE)->save();
            return $this;
        }
        $prizes = $this->getRaffle()->getPrizes();
        $exitRand = [];
        /** @var \Angel\RaffleClient\Model\Prize $_prize */
        try {
            $isWinning = false;
            foreach ($prizes as $_prize) {
                $prizeQty = $_prize->getTotalRandomNumberNeedToGenerate();
                if ($prizeQty>0){
                    for ($i = 0; $i < $prizeQty; $i++) {
                        $rand = $this->randomNumberGenerateModel->generateRand($start, $end, $exitRand);
                        if ($rand !== false && $this->getStart() <= $rand && $rand <= $this->getEnd()) {
                            /** @var \Angel\RaffleClient\Model\RandomNumber $randomNumberModel */
                            $randomNumberModel = $this->randomNumberFactory->create();
                            $randomNumberModel->setPrizeId($_prize->getId())
                                ->setNumber($rand)
                                ->setPrice($_prize->getWinningPrice());
                            $randomNumberModel->getResource()->save($randomNumberModel);
                            $isWinning = true;
                        }
                    }
                }
            }
            $this->setRevealAt((strftime('%Y-%m-%d %H:%M:%S', $this->dateTime->gmtTimestamp())));
            if ($isWinning){
                $this->setStatus(static::WON)->save();
            } else {
                $this->setStatus(static::LOSE)->save();
            }
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

    /**
     * @return float|null
     */
    public function getBasePrice(){
        return $this->getInvoiceItem()->getBasePrice();
    }

    /**
     * @return float|null
     */
    public function getPrice(){
        return $this->getInvoiceItem()->getRowTotal();
    }

    /**
     * @param $end
     * @param \Angel\RaffleClient\Model\ResourceModel\Prize\Collection $prizes
     * @param array $winner
     */
    public function checkTestCustomer($end, $prizes, &$prizesArray, &$winners){
        $exitRand = [];
        /** @var \Angel\RaffleClient\Model\Prize $prize */
        try {
            foreach ($prizes as $prize) {
                $prizeQty = $prizesArray[$prize->getId()];
                if ($prizeQty>0){
                    for ($i = 0; $i < $prizeQty; $i++) {
                        $rand = $this->randomNumberGenerateModel->generateRand($this->getStart(), $end, $exitRand);
                        if ($rand !== false && $this->getStart() <= $rand && $rand <= $this->getEnd()) {
                            $order = $this->getInvoiceItem()->getOrderItem()->getOrder();
                            if(!isset($winners[$order->getCustomerId()])){
                                $winners[$order->getCustomerId()] = ['email' => $order->getCustomerEmail(),
                                    'winning_price' => $prize->getWinningPrice(),
                                    'winning_times' => 1
                                ];
                            } else {
                                $winners[$order->getCustomerId()]['winning_price'] += $prize->getWinningPrice();
                                $winners[$order->getCustomerId()]['winning_times']++;
                            }
                            $prizesArray[$prize->getId()]--;
                        }
                    }
                }
            }
        } catch (\Exception $e){

        }
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
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer(){
        if (!$this->customer){
            $this->customer = $this->getInvoiceItem()->getInvoice()->getOrder()->getCustomer();
        }
        return $this->customer;
    }

    public function getCustomerId(){
        return $this->getInvoiceItem()->getInvoice()->getOrder()->getCustomerId();
    }

    /**
     * @return string|null
     */
    public function getOrderIncrementId(){
        return $this->getData('order_increment_id');
    }

    /**
     * @return ResourceModel\RandomNumber\Collection
     */
    public function getWinningNumberCollection(){
        if (!$this->randomNumberCollection){
            $this->randomNumberCollection = $this->getRaffle()->getRandomNumbers()
                ->addFieldToFilter('number', ['gteq' => $this->getStart()])
                ->addFieldToFilter('number', ['lteq' => $this->getEnd()]);
        }
        return $this->randomNumberCollection;
    }

    /**
     * @return string|null
     */
    public function getOrderId(){
        return $this->getData('order_id');
    }

    /**
     * @return string|null
     */
    public function getWinningNumbers(){
        return $this->getData('winning_numbers');
    }

    /**
     * @return string|null
     */
    public function getWinningNumbersFormated(){
        $winningNumbers = explode(',', $this->getWinningNumbers());
        foreach ($winningNumbers as $key => &$number){
            if ($number==''){
                unset($number);
            } else {
                $number = $this->getRaffle()->getProduct()->getRafflePrefix() . ' ' . $number;
            }
        }
        return implode(', ', $winningNumbers);
    }

    public function getWinningPrice(){
        return $this->getData('winning_price');
    }

    /**
     * Retrieve formated price
     *
     * @param float $value
     * @return string
     */
    public function formatPrice($value, $isHtml = true)
    {
        return $this->priceCurrency->format(
            $value,
            $isHtml,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            1 //Todo getStore
        );
    }

    /**
     * @return mixed
     */
    public function getCreatedAt(){
        return $this->getData('created_at');
    }

    public function getStatusLabel(){
        switch ($this->getStatus()){
            case static::NOT_CHECKED:
                return __('Not Checked');
            case static::CHECKED:
                return __('Checked');
            case static::WAIT:
                return __('Waitting');
            case static::PAID:
                return __('Paid');
            case static::WON:
                return __('Won');
            case static::LOSE:
                return __('Lose');
            default:
                return __('Enable');
        }
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
        if (!isset($this->raffle)){
            $this->raffle = $this->raffleFactory->create()->setProduct($this->getInvoiceItem()->getProductId());
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

    /**
     * Get reveal_at
     * @return string
     */
    public function getRevealAt()
    {
        return $this->getData(self::REVEAL_AT);
    }

    /**
     * Set reveal_at
     * @param string $revealAt
     * @return \Angel\RaffleClient\Api\Data\TicketInterface
     */
    public function setRevealAt($revealAt)
    {
        return $this->setData(self::REVEAL_AT, $revealAt);
    }
}
