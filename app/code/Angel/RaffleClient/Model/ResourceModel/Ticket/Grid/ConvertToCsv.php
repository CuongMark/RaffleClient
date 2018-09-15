<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Angel\RaffleClient\Model\ResourceModel\Ticket\Grid;

use Magento\Framework\Filesystem;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Model\Export\MetadataProvider;

class ConvertToCsv extends \Magento\Ui\Model\Export\ConvertToCsv
{
    /**
     * @var \Angel\RaffleClient\Model\Raffle
     */
    protected $raffle;

    protected $collectionFactory;

    public function __construct(
        Filesystem $filesystem,
        Filter $filter,
        MetadataProvider $metadataProvider,
        \Angel\RaffleClient\Model\Raffle $raffle,
        \Angel\RaffleClient\Model\ResourceModel\Ticket\CollectionFactory $collectionFactory,
        $pageSize = 200
    ){
        $this->raffle = $raffle;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($filesystem, $filter, $metadataProvider, $pageSize);
    }

    /**
     * @param int $product_id
     * @param null|string $type
     * @return array
     */
    public function getCsvFile($product_id = 0, $type = null)
    {
        $name = md5(microtime());
        $file = 'export/random_numbers_' . $name . '.csv';
        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();
        if ($product_id)
            $collection = $this->raffle->setProduct($product_id)->getTickets();
        else {
            $collection = $this->collectionFactory->create();
        }
        $collection->joinInvoiceItemTable()
            ->joinInvoiceTable()
            ->joinOrderTable()
            ->joinPrizes()
            ->joinProductName();
        if ($type){
            $collection->joinProductTypeId($type);
        }
        $stream->writeCsv(['Ticket Id', 'Customer',  'Start', 'End', 'Order Id', 'Winning Numbers', 'Winning Prizes']);
        /** @var \Angel\RaffleClient\Model\Ticket $item */
        foreach ($collection as $item){
            $data = [$item->getTicketid(), $item->getCustomerEmail(), $item->getStart(), $item->getEnd(), $item->getOrderIncrementId(), $item->getWinningNumbers(), $item->formatPrice($item->getWinningPrice(), false)];
                $stream->writeCsv($data);
        }
        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true  // can delete file after use
        ];
    }
}
