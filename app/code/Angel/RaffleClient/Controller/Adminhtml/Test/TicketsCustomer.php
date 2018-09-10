<?php


namespace Angel\RaffleClient\Controller\Adminhtml\Test;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Angel\RaffleClient\Model\Test\ConvertToCsv;
use Magento\Framework\App\Response\Http\FileFactory;

class TicketsCustomer extends Action
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
        return $this->fileFactory->create('winning_'.time().'.csv', $this->converter->generateTest($params, true), 'var');
    }
}
