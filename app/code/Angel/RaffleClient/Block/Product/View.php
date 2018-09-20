<?php


namespace Angel\RaffleClient\Block\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;

class View extends \Magento\Catalog\Block\Product\View
{
    /**
     * @var \Angel\RaffleClient\Model\RaffleFactory
     */
    protected $raffleFactory;

    /**
     * @var \Angel\RaffleClient\Model\Raffle
     */
    protected $raffle;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     * @deprecated 101.1.0
     */
    protected $priceCurrency;

    /**
     * View constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Customer\Model\Session $customerSession
     * @param ProductRepositoryInterface|\Magento\Framework\Pricing\PriceCurrencyInterface $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Angel\RaffleClient\Model\RaffleFactory $raffleFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Angel\RaffleClient\Model\RaffleFactory $raffleFactory,
        \Magento\Customer\Model\Session $customerSession,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data = []
    ){
        parent::__construct($context, $urlEncoder, $jsonEncoder, $string, $productHelper, $productTypeConfig, $localeFormat, $customerSession, $productRepository, $priceCurrency, $data);
        $this->raffleFactory = $raffleFactory;
    }

    /**
     * @return bool
     */
    public function isRaffleFiftyProduct(){
        return $this->getProduct()->getTypeId() == \Angel\RaffleClient\Model\Fifty::TYPE_ID;
    }

    /**
     * @return int
     */
    public function getRaffleTimeLeft(){
        $date = new \DateTime();
        $raffleEnd = new \DateTime($this->getProduct()->getRaffleEnd());
        return $raffleEnd->getTimestamp() - $date->getTimestamp();
    }

    public function isRaffleFinished(){
        return $this->getRaffle()->isFinished();
    }

    /**
     * @return \Angel\RaffleClient\Model\Raffle
     */
    public function getRaffle(){
        if (!isset($this->raffle)){
            $this->raffle = $this->raffleFactory->create()->setProduct($this->getProduct());
        }
        return $this->raffle;
    }

    /**
     * @return float
     */
    public function getCurrentPot(){
        return $this->getRaffle()->getCurrentPot();
    }

    public function getCurrentPotFormated(){
        return $this->priceCurrency->format($this->getCurrentPot());
    }

    public function getPriceCurrency(){
        return $this->priceCurrency;
    }

    public function getRandomNumbers(){
        return $this->getRaffle()->getRandomNumbers();
    }


}
