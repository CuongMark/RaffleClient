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
     * ConvertToCsv constructor.
     * @param Filesystem $filesystem
     * @param RandomNumberGenerate $randomNumberGenerate
     */
    public function __construct(
        Filesystem $filesystem,
        RandomNumberGenerate $randomNumberGenerate
    ) {
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->randomNumberGenerate = $randomNumberGenerate;
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
            $result[] = $this->randomNumberGenerate->randomNumberArrayGenerate(0, $total -1, $totalNumbers);
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
}
