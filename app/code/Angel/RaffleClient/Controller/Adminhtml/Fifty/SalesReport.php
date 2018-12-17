<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Angel\RaffleClient\Controller\Adminhtml\Fifty;

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
            __('50/50 Products Ordered'),
            __('50/50 Products Ordered')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Ordered 50/50 Products Report'));
        $this->_view->renderLayout();
    }
}