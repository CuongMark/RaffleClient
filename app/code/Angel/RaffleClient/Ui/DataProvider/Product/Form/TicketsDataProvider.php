<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
 namespace Angel\RaffleClient\Ui\DataProvider\Product\Form;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Cache\Frontend\Adapter\Zend;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Angel\RaffleClient\Model\ResourceModel\Ticket\CollectionFactory;
use Angel\RaffleClient\Model\ResourceModel\Ticket\Collection;
use Magento\Review\Model\Review;

/**
 * Class ReviewDataProvider
 *
 * @api
 *
 * @method Collection getCollection
 * @since 100.1.0
 */
class TicketsDataProvider extends AbstractDataProvider
{
    /**
     * @var CollectionFactory
     * @since 100.1.0
     */
    protected $collectionFactory;

    /**
     * @var RequestInterface
     * @since 100.1.0
     */
    protected $request;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collectionFactory = $collectionFactory;
        $this->collection = $this->collectionFactory->create();
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     * @since 100.1.0
     */
    public function getData()
    {
        if ($this->request->getParam('current_product_id', 0))
            $this->getCollection()->getRaffleTickets($this->request->getParam('current_product_id', 0));
        $this->getCollection()->joinInvoiceItemTable()
            ->joinInvoiceTable()
            ->joinOrderTable()
            ->joinPrizes()
            ->joinProductName();

        $arrItems = [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => [],
        ];

        foreach ($this->getCollection() as $item) {
            $arrItems['items'][] = $item->toArray([]);
        }
        return $arrItems;
    }

    /**
     * {@inheritdoc}
     * @since 100.1.0
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $field = $filter->getField();
        if (in_array($field, ['winning_price'])) {
            $filter->setField('random.' . $field);
        }

        if ($filter->getField() == 'status'){
            $this->getCollection()->addFieldToFilter(
                'main_table.'.$filter->getField(),
                [$filter->getConditionType() => $filter->getValue()]
            );
        } else {
            parent::addFilter($filter);
        }
    }
}
