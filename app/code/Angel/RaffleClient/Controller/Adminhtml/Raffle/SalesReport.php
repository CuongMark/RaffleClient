<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Angel\RaffleClient\Controller\Adminhtml\Raffle;

class SalesReport extends \Magento\Reports\Controller\Adminhtml\Report\Product
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Reports::sold';

    /**
     * Sold Products Report Action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_setActiveMenu(
            'Magento_Reports::report_products_sold'
        )->_addBreadcrumb(
            __('Raffle Products Ordered'),
            __('Raffle Products Ordered')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Ordered Raffle Products Report'));
        $this->_view->renderLayout();
    }
}
