<?php


namespace Angel\RaffleClient\Controller\Adminhtml\Test;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Angel\RaffleClient\Model\Test\ConvertToCsv;
use Magento\Framework\App\Response\Http\FileFactory;

class Save extends Action
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
     * @param Context $context
     * @param ConvertToCsv $converter
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        ConvertToCsv $converter,
        FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->converter = $converter;
        $this->fileFactory = $fileFactory;
    }

    /**
     * Export data provider to CSV
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $total = $this->getRequest()->getParams();
        if (isset($total['back'])){
            return $this->fileFactory->create('distribution_'.time().'.csv', $this->converter->getDistribution($total), 'var');
        }
        return $this->fileFactory->create('random_number_'.time().'.csv', $this->converter->getCsvFile($total), 'var');
    }
}