<?php


namespace Angel\RaffleClient\Controller\Adminhtml\Ticket;

use Magento\Backend\App\Action;
use Magento\Customer\Controller\RegistryConstants;

class Lists extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    )
    {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * Customer initialization
     *
     * @return string customer id
     */
    protected function initCurrentCustomer()
    {
        $customerId = (int)$this->getRequest()->getParam('id');

        if ($customerId) {
            $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customerId);
        }

        return $customerId;
    }

    /**
     * Customer orders grid
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $this->initCurrentCustomer();
        $resultLayout = $this->resultLayoutFactory->create();
        return $resultLayout;
    }
}
