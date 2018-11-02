<?php


namespace Angel\RaffleClient\Model;

use Magento\Framework\Validator\Exception;

class Raffle
{
    const TYPE_ID = 'raffle';

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Angel\RaffleClient\Model\PrizeFactory
     */
    protected $prizeFactory;

    /**
     * @var \Angel\RaffleClient\Model\TicketFactory TicketFactory
     */
    protected $ticketFactory;

    /**
     * @var \Angel\RaffleClient\Model\ResourceModel\Ticket\Collection
     */
    protected $ticketCollection;

    /**
     * @var \Angel\RaffleClient\Model\ResourceModel\RandomNumber\CollectionFactory
     */
    protected $randomNumberCollectionFactory;



    /**
     * @var int
     */
    protected $currentTicketNumber;

    /**
     * @var int
     */
    protected $totalRNGs;

    /**
     * @var int
     */
    protected $totalPrizes;

    /**
     * @var \Angel\RaffleClient\Model\ResourceModel\Prize\Collection
     */
    protected $prizes;

    /**
     * @var \Angel\RaffleClient\Model\ResourceModel\RandomNumber\Collection
     */
    protected $randomNumbers;

    /**
     * @var RandomNumberFactory
     */
    protected $randomNumberFactory;

    /**
     * @var RandomNumberGenerate
     */
    protected $randomNumberGenerateModel;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    protected $dateTimeFactory;

    /**
     * @return array
     */
    static function getRaffleProductTypes(){
        return [static::TYPE_ID, Fifty::TYPE_ID];
    }

    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Angel\RaffleClient\Model\PrizeFactory $prizeFactory,
        \Angel\RaffleClient\Model\TicketFactory $ticketFactory,
        \Angel\RaffleClient\Model\RandomNumberFactory $randomNumberFactory,
        \Angel\RaffleClient\Model\RandomNumberGenerate $randomNumberGenerateModel,
        \Angel\RaffleClient\Model\ResourceModel\Ticket\Collection $ticketCollection,
        \Angel\RaffleClient\Model\ResourceModel\RandomNumber\CollectionFactory $randomNumberCollectionFactory,
        \Angel\RaffleClient\Model\ResourceModel\Prize\CollectionFactory $prizeCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateTimeFactory
    ){
        $this->productFactory = $productFactory;
        $this->prizeFactory = $prizeFactory;
        $this->ticketCollection = $ticketCollection;
        $this->randomNumberCollectionFactory = $randomNumberCollectionFactory;
        $this->prizeCollectionFactory = $prizeCollectionFactory;
        $this->ticketFactory = $ticketFactory;
        $this->currentTicketNumber = false;
        $this->randomNumberFactory = $randomNumberFactory;
        $this->randomNumberGenerateModel = $randomNumberGenerateModel;
        $this->dateTimeFactory = $dateTimeFactory;
    }

    /**
     * @param \Magento\Catalog\Model\Product|int $product
     * @return $this
     */
    public function setProduct($product){
        if (!$product instanceof \Magento\Catalog\Model\Product){
            $product = $this->productFactory->create()->load($product);
        }
        if ($this->isRaffleProduct($product))
            $this->product = $product;
        return $this;
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct(){
        return $this->product;
    }

    public function getName(){
        return $this->getProduct()->getName();
    }

    public function getDescription(){
        return $this->getProduct()->getDescription();
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function isRaffleProduct($product){
        return in_array($product->getTypeId(), static::getRaffleProductTypes());
    }

    /**
     * @return array|int
     */
    public function getCurrentLargestTicketNumber(){
        if ($this->product->isObjectNew()){
            return 0;
        }
        if ($this->currentTicketNumber === false){
            $this->currentTicketNumber = $this->ticketCollection->getCurrentLargestTicketNumber($this->getProduct()->getId());
        }
        return $this->currentTicketNumber;
    }

    /**
     * @return bool
     */
    public function isAbleToCreateTicket(){
        // TODO check raffle status
        return $this->getProduct()->getId() && $this->isRaffleProduct($this->getProduct())
            && !$this->isOverTotalTicket();
    }

    /**
     * @return int
     */
    public function getTotalTicket(){
        if($this->getProduct()->getTypeId() == Fifty::TYPE_ID){
            return $this->getCurrentLargestTicketNumber();
        }
        if ($this->getProduct()->getTotalTickets()){
            return (int) $this->getProduct()->getTotalTickets();
        }
        return 0;
    }

    /**
     * @param int|null $endNumber
     * @return bool
     */
    public function isOverTotalTicket($endNumber = null){
        if ($this->getProduct()->getTypeId() == Fifty::TYPE_ID){
            return false;
        } elseif ($this->getProduct()->getTypeId() == static::TYPE_ID){
            if (!$this->getTotalTicket()){
                return true;
            } else if (!$endNumber){
                return (boolean)$this->getCurrentLargestTicketNumber() > $this->getTotalTicket();
            } else {
                return $endNumber > $this->getTotalTicket();
            }
        }
        return true;
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice\Item $invoiceItem
     * @return \Angel\RaffleClient\Model\Ticket|null
     */
    public function createTicket($invoiceItem){
        if (!$this->getProduct()){
            $this->setProduct($invoiceItem->getProductId());
        }
        try{
            $qty = $invoiceItem->getQty();
            $invoiceItemId = $invoiceItem->getId();
            if (!$this->isAbleToCreateTicket()) {
                throw new Exception(__('Unable to create ticket for %1 raffle product', $this->getProduct()->getName()));
            }
            $start = $this->getCurrentLargestTicketNumber() + 1;
            $endNumber = $start + $qty -1;
            if ($this->isOverTotalTicket($endNumber) === true){
                throw new Exception(__('The ticket number is over total ticket'));
            }
            /** @var \Angel\RaffleClient\Model\Ticket $ticket */
            $ticket = $this->ticketFactory->create();
            $ticket->setStart($start)
                ->setEnd($endNumber)
                ->setInvoiceItemId($invoiceItemId)
                ->setSerial($this->generateRandomString())
                ->setStatus(Ticket::NOT_CHECKED);
            // TODO create serial
            $ticket->getResource()->save($ticket);
            return $ticket;
        } catch (\Exception $e){
            // TOTO show exception message
        }
        return null;
    }

    protected function generateRandomString($length = 13) {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * return total random number generated in current raffle
     * @return int
     */
    public function getTotalRNGs(){
        if (!isset($this->totalRNGs)){
            /** @var \Angel\RaffleClient\Model\ResourceModel\RandomNumber\Collection $randomNumberCollection */
            $randomNumberCollection = $this->randomNumberCollectionFactory->create();
            $this->totalRNGs = $randomNumberCollection->addProductIdToFilter($this->getProduct()->getId())->getTotalRNG();
        }
        return $this->totalRNGs;
    }

    /**
     * return prizes collection of current raffle
     * @return ResourceModel\Prize\Collection
     */
    public function getPrizes(){
        if (!$this->prizes){
            /** @var \Angel\RaffleClient\Model\ResourceModel\Prize\Collection $prizeCollection */
            $prizeCollection = $this->prizeCollectionFactory->create();
            $this->prizes = $prizeCollection->addFieldToFilter('product_id', $this->getProduct()->getId())->addRNGsCount();
        }
        return $this->prizes;
    }

    /**
     * @return ResourceModel\RandomNumber\Collection
     */
    public function getRandomNumbers(){
        if (!$this->randomNumbers){
            /** @var \Angel\RaffleClient\Model\ResourceModel\RandomNumber\Collection $randomNumberCollection */
            $randomNumberCollection = $this->randomNumberCollectionFactory->create();
            $this->randomNumbers = $randomNumberCollection->addProductIdToFilter($this->getProduct()->getId());
        }
        return $this->randomNumbers;
    }

    /**
     * return total prize availabe to generate random number
     * @return int
     */
    public function getTotalPrizes(){
        if ($this->totalRNGs === false){
            /** @var \Angel\RaffleClient\Model\ResourceModel\Prize\Collection $prizeCollection */
            $prizeCollection = $this->prizeCollectionFactory->create();
            $this->totalRNGs = $prizeCollection->addFieldToFilter('product_id', $this->getProduct()->getId())->getTotalPrizes();
        }
        return $this->totalRNGs;
    }

    /**
     * @return \Angel\RaffleClient\Model\ResourceModel\Ticket\Collection
     */
    public function getTickets(){
        return $this->ticketCollection->getRaffleTickets($this->getProduct()->getId());
    }

    /**
     * @return float
     */
    public function getTotalPricePaid(){
        // todo return sum of invoice
        return $this->getTickets()->getCurrentTicketsPaid();
//        return $this->getProduct()->getPrice() * ($this->getCurrentLargestTicketNumber());
    }

    /**
     * @return float
     */
    public function getCurrentPot(){
        return ($this->getTotalPricePaid()) + $this->getProduct()->getStartPot();
    }

    public function isFinished(){
        return (boolean)$this->getTotalRNGs();
    }

    /**
     * @return int|null
     */
    public function isAbleToGenerateByTicket(){
        //Todo check raffle type
        return $this->getProduct()->getTypeId()==self::TYPE_ID;
    }

    /**
     * @return bool
     */
    public function isSaleAbleFiftyTicket(){
        if ($this->getTotalRNGs()){
            return false;
        }
        $endTime = $this->dateTimeFactory->create()->date('Y-m-d H:i:s', $this->getProduct()->getRaffleEnd());
        $now = $this->dateTimeFactory->create()->date();
        if ($now > $endTime) {
            return false;
        }
        return true;
    }

    /**
     * create and check Random Number
     */
    public function generateFiftyRaffleTicket(){
        if ($this->getProduct()->getTypeId()!=fifty::TYPE_ID
            || $this->getTotalRNGs()
            || $this->getTotalPrizes() > $this->getCurrentLargestTicketNumber()
        ){
            return $this;
        }

        $endTime = $this->dateTimeFactory->create()->date('Y-m-d H:i:s', $this->getProduct()->getRaffleEnd());
        $now = $this->dateTimeFactory->create()->date();
        if ($now < $endTime) {
            return $this;
        }

        /** random number from start */
        $start = 1;
        $end = $this->getCurrentLargestTicketNumber();
        if ($start > $end){
            return $this;
        }
        $prizes = $this->getPrizes();
        $exitRand = [];
        /** @var \Angel\RaffleClient\Model\Prize $_prize */
        try {
            foreach ($prizes as $_prize) {
                $prizeQty = $_prize->getTotal();
                if ($prizeQty>0){
                    for ($i = 0; $i < $prizeQty; $i++) {
                        $rand = $this->randomNumberGenerateModel->generateRand($start, $end, $exitRand);
                        /** @var \Angel\RaffleClient\Model\RandomNumber $randomNumberModel */
                        $randomNumberModel = $this->randomNumberFactory->create();
                        $randomNumberModel->setPrizeId($_prize->getId())
                            ->setNumber($rand)
                            ->setPrice($_prize->getWinningPrice());
                        $randomNumberModel->getResource()->save($randomNumberModel);
                    }
                }
            }
            $tickets = $this->getTickets();
            /** @var \Angel\RaffleClient\Model\Ticket $_ticket */
            foreach ($tickets as $_ticket){
                $_ticket->setStatus(Ticket::LOSE);
                foreach ($exitRand as $number){
                    if ($_ticket->getStart() <= $number && $_ticket->getEnd() >= $number){
                        $_ticket->setStatus(Ticket::WON);
                        break;
                    }
                }
                $_ticket->setRevealAt($now);
                $_ticket->save();
            }
        } catch (\Exception $e){
        }
        return $this;
    }
}
