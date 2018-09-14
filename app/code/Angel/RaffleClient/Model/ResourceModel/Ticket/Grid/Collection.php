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
    const INVOICE_TABLE = 'invoice';
    const ORDER_TABLE = 'order';
    const PRIZE_TABLE = 'prize';
    const RANDOM_NUMBER_TABLE = 'random';
    const PRODUCT_ENTITY = 'product';
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
//        $this->getRaffleTickets($this->request->getParam('product_id'));
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

    /**
     * @return $this
     */
    public function joinInvoiceItemTable(){
        if (!isset($this->isJoinedInvoieItem)) {
            $this->isJoinedInvoieItem = true;
            $this->getSelect()->joinLeft(
                [static::INVOICE_ITEM_TABLE => $this->getTable('sales_invoice_item')],
                static::INVOICE_ITEM_TABLE . '.entity_id = main_table.invoice_item_id',
                ['product_id' => static::INVOICE_ITEM_TABLE . '.product_id']
            );
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function joinInvoiceTable(){
        if (!isset($this->isJoinedInvoie)) {
            $this->isJoinedInvoie = true;
            $this->getSelect()->joinLeft(
                [static::INVOICE_TABLE => $this->getTable('sales_invoice')],
                static::INVOICE_ITEM_TABLE . '.parent_id =' . static::INVOICE_TABLE . '.entity_id',
                ['invoice_id' => static::INVOICE_TABLE . '.entity_id', 'created_at' => static::INVOICE_TABLE . '.created_at']
            );
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function joinOrderTable(){
        $this->getSelect()->joinLeft(
            [static::ORDER_TABLE => $this->getTable('sales_order')],
            static::ORDER_TABLE.'.entity_id ='.static::INVOICE_TABLE.'.order_id',
            ['customer_id' => static::ORDER_TABLE.'.customer_id', 'order_increment_id' => static::ORDER_TABLE.'.increment_id', 'order_id' => static::ORDER_TABLE.'.entity_id', 'customer_email' => static::ORDER_TABLE.'.customer_email']
        );
        return $this;
    }

    protected function joinWinningNumbers(){

    }

    /**
     * @return $this
     */
    public function joinPrizes(){
        $this->joinInvoiceItemTable();
        if (!isset($this->_isJoinedPrizes)) {
            $this->_isJoinedPrizes = true;
            $this->getSelect()->joinLeft(
                [static::PRIZE_TABLE => $this->getTable('angel_raffleclient_prize')],
                static::PRIZE_TABLE . '.product_id =' . static::INVOICE_ITEM_TABLE . '.product_id',
                []
            );
        }
        if (!isset($this->_isJoinedNumers)) {
            $this->_isJoinedNumers = true;
            $this->getSelect()->joinLeft(
                [static::RANDOM_NUMBER_TABLE => $this->getTable('angel_raffleclient_randomnumber')],
                static::RANDOM_NUMBER_TABLE . '.prize_id =' . static::PRIZE_TABLE . '.prize_id and ' . static::RANDOM_NUMBER_TABLE . '.number >= main_table.start and ' . static::RANDOM_NUMBER_TABLE . '.number <= main_table.end',
                ['winning_numbers' => 'GROUP_CONCAT(' . static::RANDOM_NUMBER_TABLE . '.number)', 'winning_price' => 'SUM(' . static::RANDOM_NUMBER_TABLE . '.price)']
            )->group('main_table.ticket_id');
        }
        return $this;
    }

    /**
     * @param string|null $type_id
     * @return $this
     */
    public function joinProductTypeId($type_id = null){
        $this->joinInvoiceItemTable();
        $this->getSelect()->joinLeft(
            [static::PRODUCT_ENTITY => $this->getTable('catalog_product_entity')],
            static::PRODUCT_ENTITY.'.entity_id = '.static::INVOICE_ITEM_TABLE .'.product_id',
            []
        );
        if ($type_id){
            $this->addFieldToFilter(static::PRODUCT_ENTITY.'.type_id', $type_id);
        }
        return $this;
    }

    public function joinProductName(){
        $productNameAttributeId = \Magento\Framework\App\ObjectManager::getInstance()->create('Magento\Eav\Model\Config')
            ->getAttribute(\Magento\Catalog\Model\Product::ENTITY, \Magento\Catalog\Api\Data\ProductInterface::NAME)
            ->getAttributeId();
        $this->getSelect()->joinLeft(['product_varchar' => $this->getTable('catalog_product_entity_varchar')],
            "product_varchar.entity_id = ".static::INVOICE_ITEM_TABLE.".product_id AND product_varchar.attribute_id = $productNameAttributeId", ['raffle_name' => 'product_varchar.value']
        );
        return $this;
    }

    /**
     * @param int $customer_id
     * @return $this
     */
    public function addCustomerFilter($customer_id){
        $this->joinInvoiceItemTable();
        $this->joinInvoiceTable();
        $this->joinOrderTable();
        $this->addFieldToFilter(static::ORDER_TABLE.'.customer_id', $customer_id);
        return $this;
    }

}
