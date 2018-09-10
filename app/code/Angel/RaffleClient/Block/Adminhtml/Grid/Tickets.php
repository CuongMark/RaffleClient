<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Angel\RaffleClient\Block\Adminhtml\Grid;

use Magento\Customer\Controller\RegistryConstants;

/**
 * Adminhtml customer orders grid block
 *
 * @api
 * @since 100.0.2
 */
class Tickets extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Sales reorder
     *
     * @var \Magento\Sales\Helper\Reorder
     */
    protected $_salesReorder = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var  \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Angel\RaffleClient\Model\ResourceModel\Ticket\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Angel\RaffleClient\Model\ResourceModel\Ticket\CollectionFactory $collectionFactory
     * @param \Magento\Sales\Helper\Reorder $salesReorder
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Angel\RaffleClient\Model\ResourceModel\Ticket\CollectionFactory $collectionFactory,
        \Magento\Sales\Helper\Reorder $salesReorder,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_salesReorder = $salesReorder;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customer_tickets_grid');
        $this->setDefaultSort('created_at', 'desc');
        $this->setUseAjax(true);
    }

    /**
     * Apply various selection filters to prepare the sales order grid collection.
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        /** @var \Angel\RaffleClient\Model\ResourceModel\Ticket\Collection $collection */
        $collection = $this->_collectionFactory->create();
        $collection->addCustomerFilter($this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn('ticket_id', ['header' => __('Ticket Id'), 'width' => '100', 'index' => 'ticket_id']);
        $this->addColumn('invoice_item', ['header' => __('Invoice Item'), 'width' => '200', 'index' => 'invoice_item_id']);
        $this->addColumn(
            'invoice_item',
            [
                'header' => 'Invoice Item',
                'filter' => false,
                'sortable' => false,
                'width' => '100px',
                'renderer' => \Angel\RaffleClient\Block\Adminhtml\Grid\InvoiceRenderer::class
            ]
        );
        $this->addColumn('start', ['header' => __('Start'), 'width' => '100', 'index' => 'start']);
        $this->addColumn('end', ['header' => __('End'), 'width' => '100', 'index' => 'end']);
        $this->addColumn('status', ['header' => __('Status'), 'width' => '100', 'index' => 'status']);

        return parent::_prepareColumns();
    }

    /**
     * Retrieve the Url for a specified sales order row.
     *
     * @param \Magento\Sales\Model\Order|\Magento\Framework\DataObject $row
     * @return string
     */
//    public function getRowUrl($row)
//    {
//        return $this->getUrl('sales/order/view', ['order_id' => $row->getId()]);
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function getGridUrl()
//    {
//        return $this->getUrl('customer/*/orders', ['_current' => true]);
//    }
}
