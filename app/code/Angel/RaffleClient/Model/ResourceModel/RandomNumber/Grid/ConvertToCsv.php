<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Angel\RaffleClient\Model\ResourceModel\RandomNumber\Grid;

use Magento\Framework\Filesystem;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Model\Export\MetadataProvider;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class ConvertToCsv extends \Magento\Ui\Model\Export\ConvertToCsv
{
    /**
     * @var \Angel\RaffleClient\Model\Raffle
     */
    protected $raffle;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Angel\RaffleClient\Model\ResourceModel\RandomNumber\CollectionFactory
     */
    protected $collectionFactory;

    public function __construct(
        Filesystem $filesystem,
        Filter $filter,
        MetadataProvider $metadataProvider,
        \Angel\RaffleClient\Model\Raffle $raffle,
        \Angel\RaffleClient\Model\ResourceModel\RandomNumber\CollectionFactory $collectionFactory,
        PriceCurrencyInterface $priceCurrency,
        $pageSize = 200
    ){
        $this->raffle = $raffle;
        $this->priceCurrency = $priceCurrency;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($filesystem, $filter, $metadataProvider, $pageSize);
    }

    public function getCsvFile($product_id = 0)
    {
        $name = md5(microtime());
        $file = 'export/random_numbers_' . $name . '.csv';
        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();
        if ($product_id)
            $collection = $this->raffle->setProduct($product_id)->getRandomNumbers();
        else {
            $collection = $this->collectionFactory->create();
        }
        $stream->writeCsv(['Raffle', 'Winning Numbers', 'Winning Prizes']);
        /** @var \Angel\RaffleClient\Model\RandomNumber $item */
        foreach ($collection as $item){
            $data = [$item->getPrizeName(), $item->getNumber(), $this->formatPrice($item->getPrice())];
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

    /**
     * Retrieve formated price
     *
     * @param float $value
     * @return string
     */
    public function formatPrice($value)
    {
        return $this->priceCurrency->format(
            $value,
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            1 //Todo getStore
        );
    }
}
