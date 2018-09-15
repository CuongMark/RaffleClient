<?php


namespace Angel\RaffleClient\Controller\Index;

class Trash extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultRedirect;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    protected $ticket;
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
        \Angel\RaffleClient\Model\Ticket $ticket
    ) {
        $this->resultRedirect = $result;
        $this->messageManager = $messageManager;
        $this->ticket = $ticket;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $ticket = $this->ticket->load($this->getRequest()->getParam('ticket_id'));
        if ($ticket->getId()) {
            $ticket->setStatus(\Angel\RaffleClient\Model\Ticket::TRASH)->save();
            $this->messageManager->addSuccessMessage(__('You moved a ticket to trash'));
        } else {
            $this->messageManager->addErrorMessage(__('The ticket does not exist'));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('raffle/index/tickets');
        return $resultRedirect;
    }
}
