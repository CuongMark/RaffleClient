<?php


namespace Angel\RaffleClient\Controller\Index;

class TransferToGift extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultRedirect;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Angel\RaffleClient\Model\TicketRepository
     */
    protected $ticketRepository;

    /**
     * @var \Magestore\Giftvoucher\Model\GiftvoucherFactory
     */
    protected $giftvoucherFactory;

    /**
     * @var \Magestore\Giftvoucher\Model\GiftCodePatternFactory
     */
    protected $modelFactory;

    /**
     * @var \Magento\Sales\Model\Order\InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * @var \Magestore\Giftvoucher\Model\CustomerVoucherFactory
     */
    protected $customerVoucherFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\ResultFactory $result,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Angel\RaffleClient\Model\TicketRepository $ticketRepository,
        \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory,
        \Magestore\Giftvoucher\Model\GiftCodePatternFactory $modelFactory,
        \Magento\Sales\Model\Order\InvoiceRepository $invoiceRepository,
        \Magestore\Giftvoucher\Model\CustomerVoucherFactory $customerVoucherFactory,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->resultRedirect = $result;
        $this->messageManager = $messageManager;
        $this->ticketRepository = $ticketRepository;
        $this->giftvoucherFactory = $giftvoucherFactory;
        $this->modelFactory = $modelFactory;
        $this->invoiceRepository = $invoiceRepository;
        $this->customerVoucherFactory = $customerVoucherFactory;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $ticket = $this->ticketRepository->getById($this->getRequest()->getParam('ticket_id'));
        if ($ticket->getId() && $ticket->getStatus() == \Angel\RaffleClient\Model\Ticket::WON && $this->customerSession->getCustomerId() == $ticket->getCustomerId()) {
            /** @var \Magestore\Giftvoucher\Model\GiftCodePattern $model */
            $model = $this->modelFactory->create();
            //ToDo check the config
            $model->load(1);
            $data = $model->getData();
            $data['conditions'] = '';
            $data['gift_code'] = $model->getData('pattern');
            $data['amount'] = $ticket->getWinningPrice();
            $data['status'] = \Magestore\Giftvoucher\Model\Status::STATUS_ACTIVE;
            $data['extra_content'] = __('Created by ticket %1', $ticket->getId());
            /** @var \Magento\Sales\Model\Order\Invoice $invoice */
            $invoice = $this->invoiceRepository->get($ticket->getInvoiceItem()->getParentId());
            /** @var \Magento\Sales\Model\Order $order */
            $order = $invoice->getOrder();
            $data['customer_id'] = $order->getCustomerId();
            $data['customer_name'] = $order->getCustomerFirstname().' '.$order->getCustomerLastname();
            $data['customer_email'] = $order->getCustomerEmail();
            $giftcode = $this->giftvoucherFactory->create();
            $giftcode->setData($data)->loadPost($data)
                ->setIncludeHistory(true)
                ->setGenerateGiftcode(true)
                ->save();
            $this->customerVoucherFactory->create()->addData(['voucher_id'=>$giftcode->getId(), 'customer_id' => $order->getCustomerId()])->save();
            /** @var \Magestore\Giftvoucher\Model\Giftvoucher $giftVoucher */
            $ticket->setStatus(\Angel\RaffleClient\Model\Ticket::PAID);
            $this->ticketRepository->save($ticket);
            $this->messageManager->addSuccessMessage(__('You transfered winning ticket to Gift Card code: %1', $giftcode->getGiftCode()));
        } else {
            $this->messageManager->addErrorMessage(__('The ticket does not exist'));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('giftvoucher/index/index');
        return $resultRedirect;
    }
}
