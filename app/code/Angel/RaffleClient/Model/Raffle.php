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
     * @return array
     */
    static function getRaffleProductTypes(){
        return [static::TYPE_ID, Fifty::TYPE_ID];
    }

    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Angel\RaffleClient\Model\PrizeFactory $prizeFactory,
        \Angel\RaffleClient\Model\TicketFactory $ticketFactory,
        \Angel\RaffleClient\Model\ResourceModel\Ticket\Collection $ticketCollection,
        \Angel\RaffleClient\Model\ResourceModel\RandomNumber\CollectionFactory $randomNumberCollectionFactory,
        \Angel\RaffleClient\Model\ResourceModel\Prize\CollectionFactory $prizeCollectionFactory
    ){
        $this->productFactory = $productFactory;
        $this->prizeFactory = $prizeFactory;
        $this->ticketCollection = $ticketCollection;
        $this->randomNumberCollectionFactory = $randomNumberCollectionFactory;
        $this->prizeCollectionFactory = $prizeCollectionFactory;
        $this->ticketFactory = $ticketFactory;
        $this->currentTicketNumber = false;
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
            return -1;
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
        return $this->product->getId() && $this->isRaffleProduct($this->getProduct())
            && !$this->isOverTotalTicket();
    }

    /**
     * @return int
     */
    public function getTotalTicket(){
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
                return $this->getCurrentLargestTicketNumber() > $this->getTotalTicket();
            } else {
                return $endNumber >= $this->getTotalTicket();
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
            $endNumber = $this->getCurrentLargestTicketNumber() + $qty;
            if ($this->isOverTotalTicket($endNumber)){
                throw new Exception(__('The ticket number is over total ticket'));
            }
            /** @var \Angel\RaffleClient\Model\Ticket $ticket */
            $ticket = $this->ticketFactory->create();
            $ticket->setStart($start)
                ->setEnd($endNumber)
                ->setInvoiceItemId($invoiceItemId)
                ->setSerial('')
                ->setStatus(Ticket::NOT_CHECKED);
            // TODO create serial
            $ticket->getResource()->save($ticket);
            return $ticket;
        } catch (\Exception $e){
            \Zend_Debug::dump($e->getMessage());
            // TOTO show exception message
        }
        return null;
    }

    /**
     * return total random number generated in current raffle
     * @return int
     */
    public function getTotalRNGs(){
        if ($this->totalRNGs === false){
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

    public function getTickets(){
        return $this->ticketCollection->getRaffleTickets($this->getProduct()->getId());
    }

    /**
     * @return float
     */
    public function getTotalPricePaid(){
        // todo return sum of invoice
        return $this->getProduct()->getPrice() * ($this->getCurrentLargestTicketNumber() + 1);
    }

    /**
     * @return int|null
     */
    public function isAbleToGenerateByTicket(){
        //Todo check raffle type
        return true;
        return $this->getProduct()->getData('raffle_type');
    }
}
