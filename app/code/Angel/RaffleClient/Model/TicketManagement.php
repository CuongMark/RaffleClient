<?php


namespace Angel\RaffleClient\Model;

class TicketManagement
{

    /**
     * @var \Angel\RaffleClient\Model\PrizeFactory
     */
    protected $prizeFactory;

    /**
     * @var \Angel\RaffleClient\Model\ResourceModel\Ticket\Collection
     */
    protected $ticketCollection;

    public function __construct(
        \Angel\RaffleClient\Model\PrizeFactory $prizeFactory,
        \Angel\RaffleClient\Model\ResourceModel\Ticket\Collection $ticketCollection
    ){
        $this->prizeFactory = $prizeFactory;
        $this->ticketCollection = $ticketCollection;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param int $qty
     * @return \Angel\RaffleClient\Model\Ticket
     */
    public function createTicket($product, $qty){
        /** @var \Angel\RaffleClient\Model\ResourceModel\Ticket\Collection $ticketCollection */
        $ticketCollection = $this->ticketCollection->getCurrentLargestTicketNumber();
        return ;
    }

    /**
     * {@inheritdoc}
     */
    public function getTicket($param)
    {
        return 'hello api GET return the $param ' . $param;
    }
}
