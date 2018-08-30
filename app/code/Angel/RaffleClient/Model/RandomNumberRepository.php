<?php


namespace Angel\RaffleClient\Model;

use Magento\Framework\Api\DataObjectHelper;
use Angel\RaffleClient\Model\ResourceModel\RandomNumber as ResourceRandomNumber;
use Angel\RaffleClient\Api\RandomNumberRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Angel\RaffleClient\Api\Data\RandomNumberSearchResultsInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Angel\RaffleClient\Api\Data\RandomNumberInterfaceFactory;
use Magento\Framework\Reflection\DataObjectProcessor;
use Angel\RaffleClient\Model\ResourceModel\RandomNumber\CollectionFactory as RandomNumberCollectionFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;

class RandomNumberRepository implements RandomNumberRepositoryInterface
{

    private $storeManager;
    protected $dataRandomNumberFactory;

    protected $randomNumberCollectionFactory;

    protected $resource;

    protected $searchResultsFactory;

    protected $randomNumberFactory;

    protected $dataObjectProcessor;

    protected $dataObjectHelper;


    /**
     * @param ResourceRandomNumber $resource
     * @param RandomNumberFactory $randomNumberFactory
     * @param RandomNumberInterfaceFactory $dataRandomNumberFactory
     * @param RandomNumberCollectionFactory $randomNumberCollectionFactory
     * @param RandomNumberSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceRandomNumber $resource,
        RandomNumberFactory $randomNumberFactory,
        RandomNumberInterfaceFactory $dataRandomNumberFactory,
        RandomNumberCollectionFactory $randomNumberCollectionFactory,
        RandomNumberSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->randomNumberFactory = $randomNumberFactory;
        $this->randomNumberCollectionFactory = $randomNumberCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataRandomNumberFactory = $dataRandomNumberFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Angel\RaffleClient\Api\Data\RandomNumberInterface $randomNumber
    ) {
        /* if (empty($randomNumber->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $randomNumber->setStoreId($storeId);
        } */
        try {
            $this->resource->save($randomNumber);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the randomNumber: %1',
                $exception->getMessage()
            ));
        }
        return $randomNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($randomNumberId)
    {
        $randomNumber = $this->randomNumberFactory->create();
        $this->resource->load($randomNumber, $randomNumberId);
        if (!$randomNumber->getId()) {
            throw new NoSuchEntityException(__('RandomNumber with id "%1" does not exist.', $randomNumberId));
        }
        return $randomNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->randomNumberCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $fields[] = $filter->getField();
                $condition = $filter->getConditionType() ?: 'eq';
                $conditions[] = [$condition => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
        
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Angel\RaffleClient\Api\Data\RandomNumberInterface $randomNumber
    ) {
        try {
            $this->resource->delete($randomNumber);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the RandomNumber: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($randomNumberId)
    {
        return $this->delete($this->getById($randomNumberId));
    }
}
