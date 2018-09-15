<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Angel\RaffleClient\Controller\Adminhtml\RandomNumber;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Angel\RaffleClient\Model\ResourceModel\RandomNumber\Grid\ConvertToCsv;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

/**
 * Class Render
 */
class ExportCSV extends Action
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
     * @var Filter
     */
    private $filter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Context $context
     * @param ConvertToCsv $converter
     * @param FileFactory $fileFactory
     * @param Filter|null $filter
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Context $context,
        ConvertToCsv $converter,
        FileFactory $fileFactory,
        Filter $filter = null,
        LoggerInterface $logger = null
    ) {
        parent::__construct($context);
        $this->converter = $converter;
        $this->fileFactory = $fileFactory;
        $this->filter = $filter
            ?: ObjectManager::getInstance()->get(Filter::class);
        $this->logger = $logger
            ?: ObjectManager::getInstance()->get(LoggerInterface::class);
    }

    /**
     * Export data provider to CSV
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $productId = $this->getRequest()->getParam('id');
        return $this->fileFactory->create('winning_number.csv', $this->converter->getCsvFile($productId), 'var');
    }

    /**
     * Checking if the user has access to requested component.
     *
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        return true;
    }
}
