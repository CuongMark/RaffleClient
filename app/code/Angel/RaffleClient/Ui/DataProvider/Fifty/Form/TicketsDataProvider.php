<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Angel\RaffleClient\Ui\DataProvider\Fifty\Form;

use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Angel\RaffleClient\Model\ResourceModel\Ticket\CollectionFactory;
use Angel\RaffleClient\Model\ResourceModel\Ticket\Collection;
use Magento\Review\Model\Review;
use Magento\Framework\UrlInterface;

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
     * @var UrlInterface
     */
    protected $urlBuilder;

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
        UrlInterface $urlBuilder,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collectionFactory = $collectionFactory;
        $this->collection = $this->collectionFactory->create();
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     * @since 100.1.0
     */
    public function getData()
    {
        if ($this->request->getParam('product_id'))
            $this->getCollection()->getRaffleTickets($this->request->getParam('product_id'));

        $this->getCollection()->joinProductTypeId(\Angel\RaffleClient\Model\Fifty::TYPE_ID);

        $arrItems = [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => [],
        ];

        foreach ($this->getCollection() as $item) {
            $arrItems['items'][] = $item->toArray([]);
        }

        $configData = $this->getConfigData();
        $configData['update_url'] = $this->urlBuilder->getUrl('mui/index/render', ['product_id' => $this->request->getParam('product_id')]);
        $this->setConfigData($configData);

        return $arrItems;
    }
}
