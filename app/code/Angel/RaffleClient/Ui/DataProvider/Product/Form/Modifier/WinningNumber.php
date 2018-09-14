<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Angel\RaffleClient\Ui\DataProvider\Product\Form\Modifier;

/**
 * Class Review
 */
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\Component\Form;
use Magento\Framework\UrlInterface;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\App\ObjectManager;

/**
 * Review modifier for catalog product form
 *
 * @api
 * @since 100.1.0
 */
class WinningNumber extends AbstractModifier
{
    const GROUP_REVIEW = 'raffle_winning_number';
    const GROUP_CONTENT = 'content';
    const DATA_SCOPE_REVIEW = 'grouped';
    const SORT_ORDER = 20;
    const LINK_TYPE = 'associated';

    protected $_fieldsetContent = 'Winning Numbers';

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var LocatorInterface
     * @since 100.1.0
     */
    protected $locator;

    /**
     * @var UrlInterface
     * @since 100.1.0
     */
    protected $urlBuilder;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @param LocatorInterface $locator
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        LocatorInterface $locator,
        \Magento\Framework\App\RequestInterface $request,
        UrlInterface $urlBuilder
    ) {
        $this->locator = $locator;
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     * @since 100.1.0
     */
    public function modifyMeta(array $meta)
    {
        if (!$this->locator->getProduct()->getId() || !$this->getModuleManager()->isOutputEnabled('Magento_Review')) {
            return $meta;
        }

        $meta[static::GROUP_REVIEW] = [
            'children' => [
                'export_button' => $this->getCustomButtons(),
                'angel_raffleclient_randomnumber_index' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => true,
                                'componentType' => 'insertListing',
                                'dataScope' => 'angel_raffleclient_randomnumber_index',
                                'externalProvider' => 'angel_raffleclient_randomnumber_index.angel_raffleclient_randomnumber_index_data_source',
                                'selectionsProvider' => 'angel_raffleclient_randomnumber_index.angel_raffleclient_randomnumber_index.product_columns.ids',
                                'ns' => 'angel_raffleclient_randomnumber_index',
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => false,
                                'behaviourType' => 'simple',
                                'externalFilterMode' => true,
                                'imports' => [
                                    'productId' => '${ $.provider }:data.product.current_product_id'
                                ],
                                'exports' => [
                                    'productId' => '${ $.externalProvider }:params.current_product_id'
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Winning Numbers'),
                        'collapsible' => true,
                        'opened' => false,
                        'componentType' => Form\Fieldset::NAME,
                        'sortOrder' =>
                            $this->getNextGroupSortOrder(
                                $meta,
                                static::GROUP_CONTENT,
                                static::SORT_ORDER
                            ),
                    ],
                ],
            ],
        ];

        return $meta;
    }

    /**
     * {@inheritdoc}
     * @since 100.1.0
     */
    public function modifyData(array $data)
    {
        $productId = $this->locator->getProduct()->getId();

        $data[$productId][self::DATA_SOURCE_DEFAULT]['current_product_id'] = $productId;

        return $data;
    }

    /**
     * Retrieve module manager instance using dependency lookup to keep this class backward compatible.
     *
     * @return ModuleManager
     *
     * @deprecated 100.2.0
     */
    private function getModuleManager()
    {
        if ($this->moduleManager === null) {
            $this->moduleManager = ObjectManager::getInstance()->get(ModuleManager::class);
        }
        return $this->moduleManager;
    }

    /**
     * Returns Buttons Set configuration
     *
     * @return array
     */
    protected function getCustomButtons()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => false,
                        'content' => __($this->_fieldsetContent),
                        'template' => 'Angel_RaffleClient/form/components/button-list',
                    ],
                ],
            ],
            'children' => [
                'download_summary_button' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Angel_RaffleClient/js/form/components/export-button',
                                'actions' => [
                                    [
                                        'render_url' => $this->urlBuilder->getUrl('angel_raffleclient/randomnumber/exportCSV', ['id' => $this->request->getParam('id')]),
                                    ]
                                ],
                                'title' => __('Export To CSV'),
                                'provider' => null,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
