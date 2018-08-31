<?php


namespace Angel\RaffleClient\Controller\Index;

class Tickets extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;

    /**
     * @var \Angel\RaffleClient\Model\PrizeFactory
     */
    protected $prizeFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Angel\RaffleClient\Model\PrizeFactory $prizeFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->prizeFactory = $prizeFactory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = [
            'label' => 'â',
            'price' => 5,
            'price_type' => 'fixed',
            'total' => 3,
            'sort_order' => 3,
        ];
        $this->prizeFactory->create()->setData($data)->save();
        return $this->resultPageFactory->create();
    }
}
