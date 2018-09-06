<?php


namespace Angel\RaffleClient\Model\Test;

use Angel\RaffleClient\Model\ResourceModel\Test\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    protected $dataPersistor;

    protected $loadedData;
    protected $collection;


    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $model) {
            $this->loadedData[$model->getId()] = $model->getData();
        }
        $data = $this->dataPersistor->get('angel_raffleclient_test');

        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->loadedData[$model->getId()]['Total'] = 10000;
            $this->loadedData[$model->getId()]['total_numbers'] = 1000;
            $this->loadedData[$model->getId()]['total_raffles'] = 10000;
            $this->dataPersistor->clear('angel_raffleclient_test');
        }
        $this->loadedData[0]['Total'] = 10000;
        $this->loadedData[0]['total_numbers'] = 1000;
        $this->loadedData[0]['total_raffles'] = 10000;

        return $this->loadedData;
    }
}
