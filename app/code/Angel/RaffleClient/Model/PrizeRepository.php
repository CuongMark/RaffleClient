<?php


namespace Angel\RaffleClient\Model;

use Magento\Framework\Api\DataObjectHelper;
use Angel\RaffleClient\Model\ResourceModel\Prize as ResourcePrize;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Angel\RaffleClient\Api\Data\PrizeInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Angel\RaffleClient\Api\PrizeRepositoryInterface;
use Angel\RaffleClient\Model\ResourceModel\Prize\CollectionFactory as PrizeCollectionFactory;
use Angel\RaffleClient\Api\Data\PrizeSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;

class PrizeRepository implements PrizeRepositoryInterface
{

    protected $dataPrizeFactory;

    protected $dataObjectHelper;

    protected $prizeFactory;

    protected $resource;

    protected $searchResultsFactory;

    protected $prizeCollectionFactory;

    protected $dataObjectProcessor;

    private $storeManager;

    /**
     * @param ResourcePrize $resource
     * @param PrizeFactory $prizeFactory
     * @param PrizeInterfaceFactory $dataPrizeFactory
     * @param PrizeCollectionFactory $prizeCollectionFactory
     * @param PrizeSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourcePrize $resource,
        PrizeFactory $prizeFactory,
        PrizeInterfaceFactory $dataPrizeFactory,
        PrizeCollectionFactory $prizeCollectionFactory,
        PrizeSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->prizeFactory = $prizeFactory;
        $this->prizeCollectionFactory = $prizeCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPrizeFactory = $dataPrizeFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Angel\RaffleClient\Api\Data\PrizeInterface $prize
    ) {
        /* if (empty($prize->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $prize->setStoreId($storeId);
        } */
        try {
            $this->resource->save($prize);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the prize: %1',
                $exception->getMessage()
            ));
        }
        return $prize;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($prizeId)
    {
        $prize = $this->prizeFactory->create();
        $this->resource->load($prize, $prizeId);
        if (!$prize->getId()) {
            throw new NoSuchEntityException(__('Prize with id "%1" does not exist.', $prizeId));
        }
        return $prize;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->prizeCollectionFactory->create();
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
        \Angel\RaffleClient\Api\Data\PrizeInterface $prize
    ) {
        try {
            $this->resource->delete($prize);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Prize: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($prizeId)
    {
        return $this->delete($this->getById($prizeId));
    }
}
