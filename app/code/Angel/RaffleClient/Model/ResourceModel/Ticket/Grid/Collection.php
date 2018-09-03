<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Angel\RaffleClient\Model\ResourceModel\Ticket\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    const INVOICE_ITEM_TABLE = 'invoice_item';
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        \Magento\Framework\App\RequestInterface $request,
        $mainTable = 'angel_raffleclient_ticket',
        $resourceModel = 'Angel\RaffleClient\Model\ResourceModel\Ticket'
    ){
        $this->request = $request;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getRaffleTickets($this->request->getParam('product_id'));
        return $this;
    }

    /**
     * @return $this
     */
    protected function joinInvoiceItemTable(){
        $this->getSelect()->joinLeft(
            [static::INVOICE_ITEM_TABLE => $this->getTable('sales_invoice_item')],
            static::INVOICE_ITEM_TABLE.'.entity_id = main_table.invoice_item_id',
            ['product_id' => static::INVOICE_ITEM_TABLE.'.product_id']
        );
        return $this;
    }

    /**
     * @param int $productId
     * @return $this
     */
    public function getRaffleTickets($productId){
        $this->joinInvoiceItemTable();
        if ($productId)
            $this->addFieldToFilter(static::INVOICE_ITEM_TABLE.'.product_id', $productId);
        return $this;
    }

}
