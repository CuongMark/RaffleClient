<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Angel\RaffleClient\Model\ResourceModel\RandomNumber\Grid;

use Magento\Framework\Filesystem;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Model\Export\MetadataProvider;

class ConvertToCsv extends \Magento\Ui\Model\Export\ConvertToCsv
{
    /**
     * @var \Angel\RaffleClient\Model\Raffle
     */
    protected $raffle;

    public function __construct(
        Filesystem $filesystem,
        Filter $filter,
        MetadataProvider $metadataProvider,
        \Angel\RaffleClient\Model\Raffle $raffle,
        $pageSize = 200
    ){
        $this->raffle = $raffle;
        parent::__construct($filesystem, $filter, $metadataProvider, $pageSize);
    }

    public function getCsvFile($product_id = 0)
    {
        $name = md5(microtime());
        $file = 'export/random_numbers_' . $name . '.csv';
        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();
        $collection = $this->raffle->setProduct($product_id)->getTickets();
        $stream->writeCsv(['Raffle Name', 'Winning Numbers', 'Winning Prizes']);
        /** @var \Angel\RaffleClient\Model\Ticket $item */
        foreach ($collection as $item){
            $data = [$item->getRaffle()->getName(), $item->getWinningNumbers(), $item->getPrice()];
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
