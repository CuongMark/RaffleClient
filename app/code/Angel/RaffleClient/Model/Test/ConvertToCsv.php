<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Angel\RaffleClient\Model\Test;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Cache\Frontend\Adapter\Zend;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Model\Export\MetadataProvider;
use Angel\RaffleClient\Model\RandomNumberGenerate;

/**
 * Class ConvertToCsv
 */
class ConvertToCsv
{
    /**
     * @var WriteInterface
     */
    protected $directory;

    /**
     * @var RandomNumberGenerate
     */
    protected $randomNumberGenerate;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Angel\RaffleClient\Model\RaffleFactory
     */
    protected $raffleFactory;

    /**
     * ConvertToCsv constructor.
     * @param Filesystem $filesystem
     * @param RandomNumberGenerate $randomNumberGenerate
     */
    public function __construct(
        Filesystem $filesystem,
        RandomNumberGenerate $randomNumberGenerate,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Angel\RaffleClient\Model\RaffleFactory $raffleFactory
    ) {
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->randomNumberGenerate = $randomNumberGenerate;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->raffleFactory = $raffleFactory;
    }

    /**
     * Returns CSV file
     *
     * @return array
     * @throws LocalizedException
     */
    public function getCsvFile($params)
    {
        $name = md5(microtime());
        $file = 'export/random_numbers_' . $name . '.csv';
        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();
        $randoms = $this->generate( $params['Total'], $params['total_numbers'], $params['total_raffles']);
        foreach ($randoms as $item){
            if ($item)
                $stream->writeCsv($item);
        }
        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true  // can delete file after use
        ];
    }

    public function generate($total, $totalNumbers, $totalRaffles){
        $result = [];
        for($i=0;$i<$totalRaffles;$i++){
            $result[] = $this->randomNumberGenerate->randomNumberArrayGenerate(1, $total, $totalNumbers);
        }
        return $result;
    }

    public function getDistribution($params){
        $name = md5(microtime());
        $file = 'export/random_numbers_' . $name . '.csv';
        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();
        $randoms = $this->generate( $params['Total'], $params['total_numbers'], $params['total_raffles']);
        $result = [];
        foreach ($randoms as $rands){
            foreach ($rands as $numb ){
                if (!isset($result[$numb])){
                    $result[$numb] = 1;
                } else {
                    $result[$numb]++;
                }
            }
        }
        $randoms = [];
        ksort($result);
        foreach ($result as $key => $item){
            $randoms[] = [$key, $item];
        }
        unset($result);
        foreach ($randoms as $item){
            if ($item)
                $stream->writeCsv($item);
        }
        unset($randoms);
        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true  // can delete file after use
        ];
    }

    public function generateTest($params, $isWinner = false){
        $name = md5(microtime());
        $file = 'export/random_numbers_' . $name . '.csv';
        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();

        $collection = $this->productCollectionFactory->create();
        if (isset($params['selected']))
            $collection->addAttributeToFilter('entity_id', $params['selected']);
        $collection->addAttributeToFilter('type_id', ['in'=> \Angel\RaffleClient\Model\Raffle::getRaffleProductTypes()]);
        $collection->addAttributeToSelect(['name', 'total_tickets']);
        foreach ($collection as $product){
            if ($isWinner){
                $this->generateRaffleCustomerTest($product, $params['total_time'], $stream);
            } else {
                $this->generateRaffleTest($product, $params['total_time'], $stream);
            }
        }

        $stream->unlock();
        $stream->close();
        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true  // can delete file after use
        ];
    }

    public function generateDistributionTest($params, $isWinner = false){
        $name = md5(microtime());
        $file = 'export/random_numbers_' . $name . '.csv';
        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();

        $collection = $this->productCollectionFactory->create();
        if (isset($params['selected']))
            $collection->addAttributeToFilter('entity_id', $params['selected']);
        $collection->addAttributeToFilter('type_id', ['in'=> \Angel\RaffleClient\Model\Raffle::getRaffleProductTypes()]);
        $collection->addAttributeToSelect(['name', 'total_tickets']);
        foreach ($collection as $product){
            $raffle = $this->raffleFactory->create()->setProduct($product);
            $prizes = $raffle->getPrizes()->getTotalPrizesItems();
            $tickets = $raffle->getTickets();
            $stream->writeCsv([$product->getName()]);
            $totalTickets = $raffle->getTotalTicket() - 1;
            $distribution = [];
            for ($i=0;$i < $params['total_time']; $i++) {
                /** @var \Angel\RaffleClient\Model\Ticket $ticket */
                $result = $rand = [];
                $dulicatePrizes = $prizes;
                foreach ($tickets as $ticket) {
                    $rand = $ticket->checkTest($totalTickets, $dulicatePrizes);
                    $result = array_merge($rand, $result);
                }
                foreach ($result as $num){
                    if (isset($distribution[$num])){
                        $distribution[$num]['total']++;
                    }else {
                        $distribution[$num] = ['number' => $num, 'total' => 1];
                    }
                }
            }
            ksort($distribution);
            foreach ($distribution as $item){
                $stream->writeCsv($item);
            }
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
     * @param \Magento\Catalog\Model\Product $product
     * @param int $totalTimes
     * @param \Magento\Framework\Filesystem\File\WriteInterface $stream
     */
    public function generateRaffleTest($product, $totalTimes,  &$stream){
        $raffle = $this->raffleFactory->create()->setProduct($product);
        $prizes = $raffle->getPrizes()->getTotalPrizesItems();
        $tickets = $raffle->getTickets();
        $stream->writeCsv([$product->getName()]);
        $totalTickets = $raffle->getTotalTicket() - 1;
        for ($i=0;$i < $totalTimes; $i++) {
            /** @var \Angel\RaffleClient\Model\Ticket $ticket */
            $result = $rand = [];
            $dulicatePrizes = $prizes;
            foreach ($tickets as $ticket) {
                $rand = $ticket->checkTest($totalTickets, $dulicatePrizes);
                $result = array_merge($rand, $result);
            }
            $stream->writeCsv($result);
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param int $totalTimes
     * @param \Magento\Framework\Filesystem\File\WriteInterface $stream
     */
    public function generateRaffleCustomerTest($product, $totalTimes,  &$stream){
        $raffle = $this->raffleFactory->create()->setProduct($product);
        $tickets = $raffle->getTickets();
        $stream->writeCsv([$product->getName()]);
        $totalTickets = $raffle->getTotalTicket() - 1;
        $winners = $prizesArray = [];
        foreach ($raffle->getPrizes() as $prize){
            $prizesArray[$prize->getId()] = $prize->getTotal();
        }
        for ($i=0;$i < $totalTimes; $i++) {
            $dulicatePrizesArray = $prizesArray;
            /** @var \Angel\RaffleClient\Model\Ticket $ticket */
            foreach ($tickets as $ticket) {
                $ticket->checkTestCustomer($totalTickets, $raffle->getPrizes(), $dulicatePrizesArray, $winners);
            }
        }
        $stream->writeCsv([__('Customer Email'), __('Total Winning Price'), __('Total Winning Times')]);
        foreach ($winners as $winner){
            $stream->writeCsv($winner);
        }
    }
}
