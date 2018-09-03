<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Angel\RaffleClient\Model\ResourceModel\RandomNumber\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    const PRIZE_TABLE = 'prize';
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
        $mainTable = 'angel_raffleclient_randomnumber',
        $resourceModel = 'Angel\RaffleClient\Model\ResourceModel\RandomNumber'
    ){
        $this->request = $request;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addProductIdToFilter($this->request->getParam('product_id'));
        return $this;
    }

    /**
     * @return $this
     */
    public function addProductIdToFilter($productId){
        $this->getSelect()->joinLeft(
            [static::PRIZE_TABLE => $this->getTable('angel_raffleclient_prize')],
            static::PRIZE_TABLE.'.prize_id = main_table.prize_id',
            ['product_id' => static::PRIZE_TABLE.'.product_id']
        );
        if ($productId)
            $this->getSelect()->where(static::PRIZE_TABLE.'.product_id = ?', $productId);
        return $this;
    }

}
