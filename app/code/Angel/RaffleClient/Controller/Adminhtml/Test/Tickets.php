<?php


namespace Angel\RaffleClient\Controller\Adminhtml\Test;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Angel\RaffleClient\Model\Test\ConvertToCsv;
use Magento\Framework\App\Response\Http\FileFactory;

class Tickets extends Action
{

    /**
     * @var ConvertToCsv
     */
    protected $converter;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $productCollection;

    /**
     * @var \Angel\RaffleClient\Model\RaffleFactory
     */
    protected $raffleFactory;
    /**
     * @param Context $context
     * @param ConvertToCsv $converter
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        ConvertToCsv $converter,
        FileFactory $fileFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection,
        \Angel\RaffleClient\Model\RaffleFactory $raffleFactory
    ) {
        parent::__construct($context);
        $this->converter = $converter;
        $this->fileFactory = $fileFactory;
        $this->productCollection = $productCollection;
        $this->raffleFactory = $raffleFactory;
    }

    /**
     * Export data provider to CSV
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        if (isset($params['selected']))
            $this->productCollection->addAttributeToFilter('entity_id', $params['selected']);
        $this->productCollection->addAttributeToFilter('type_id', ['in'=> \Angel\RaffleClient\Model\Raffle::getRaffleProductTypes()]);

        foreach ($this->productCollection as $product){
            $raffle = $this->raffleFactory->create()->setProduct($product->getId());
            $prizes = $raffle->getPrizes()->getTotalPrizesItems();
            $tickets = $raffle->getTickets();
            /** @var \Angel\RaffleClient\Model\Ticket $ticket */
            $start = 0;
            $end = $raffle->getTotalTicket() - 1;
            $result = $rand = [];
            foreach ($tickets as $ticket){
                $rand = $ticket->checkTest($end, $prizes);
            }
            $result = array_merge($result, $rand);
            \Zend_Debug::dump($prizes);
            \Zend_Debug::dump($result);
            die('dasfdasf');
        }
    }
}
