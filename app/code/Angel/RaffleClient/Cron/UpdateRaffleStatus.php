<?php


namespace Angel\RaffleClient\Cron;

class UpdateRaffleStatus
{
    /**
     * @var \Angel\RaffleClient\Model\RaffleFactory
     */
    protected $raffleFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    protected $dateTimeFactory;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Angel\RaffleClient\Model\RaffleFactory $raffleFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateTimeFactory
    ){
        $this->logger = $logger;
        $this->raffleFactory = $raffleFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->dateTimeFactory = $dateTimeFactory;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $now = $this->dateTimeFactory->create()->date();
        $collection = $this->productCollectionFactory->create()
            ->addAttributeToFilter('type_id', \Angel\RaffleClient\Model\Fifty::TYPE_ID)
            ->addAttributeToFilter('raffle_end', ['gteq' => $now])
            ->addAttributeToSelect('*');
        foreach ($collection as $product){
            $this->raffleFactory->create()->setProduct($product)->generateFiftyRaffleTicket();
        }
        $this->logger->addInfo('Cronjob update Raffle Status is executed at '.$now);
    }
}
