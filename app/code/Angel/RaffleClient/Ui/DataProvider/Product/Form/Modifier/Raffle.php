<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Angel\RaffleClient\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ProductOptions\ConfigInterface;
use Magento\Catalog\Model\Config\Source\Product\Options\Price as ProductOptionsPrice;
use Magento\Framework\UrlInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\ActionDelete;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\DataType\Number;

class Raffle extends AbstractModifier
{
    /**#@+
     * Group values
     */
    const GROUP_RAFFLE_NAME = 'raffle';
    const GROUP_RAFFLE_SCOPE = 'data.product';
    const GROUP_RAFFLE_PREVIOUS_NAME = 'general';
    const GROUP_RAFFLE_DEFAULT_SORT_ORDER = 2;

    /**#@+
     * Grid values
     */
    const GRID_PRIZE_NAME = 'prizes';
    const GRID_TYPE_SELECT_NAME = 'prizes';

    /**#@+
     * Field values
     */
    const FIELD_ENABLE = 'affect_product_custom_options';
    const FIELD_PRIZE_ID_NAME = 'prize_id';
    const FIELD_LABEL_NAME = 'label';
    const FIELD_STORE_TITLE_NAME = 'store_title';
    const FIELD_TYPE_NAME = 'type';
    const FIELD_IS_REQUIRE_NAME = 'is_require';
    const FIELD_SORT_ORDER_NAME = 'sort_order';
    const FIELD_PRICE_NAME = 'price';
    const FIELD_PRICE_TYPE_NAME = 'price_type';
    const FIELD_TOTAL_NAME = 'total';
    const FIELD_IS_DELETE = 'is_delete';
    const FIELD_IS_USE_DEFAULT = 'is_use_default';
    /**
     * @var array
     * @since 101.0.0
     */
    protected $meta = [];

    /**
     * @var LocatorInterface
     * @since 101.0.0
     */
    protected $locator;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     * @since 101.0.0
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\ProductOptions\ConfigInterface
     * @since 101.0.0
     */
    protected $productOptionsConfig;

    /**
     * @var \Magento\Catalog\Model\Config\Source\Product\Options\Price
     * @since 101.0.0
     */
    protected $productOptionsPrice;

    /**
     * @var UrlInterface
     * @since 101.0.0
     */
    protected $urlBuilder;

    /**
     * @var ArrayManager
     * @since 101.0.0
     */
    protected $arrayManager;

    protected $prizeCollection;


    /**
     * @param LocatorInterface $locator
     * @param StoreManagerInterface $storeManager
     * @param ConfigInterface $productOptionsConfig
     * @param ProductOptionsPrice $productOptionsPrice
     * @param UrlInterface $urlBuilder
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        LocatorInterface $locator,
        StoreManagerInterface $storeManager,
        ConfigInterface $productOptionsConfig,
        ProductOptionsPrice $productOptionsPrice,
        UrlInterface $urlBuilder,
        ArrayManager $arrayManager,
        \Angel\RaffleClient\Model\ResourceModel\Prize\Collection $prizeCollection
    ) {
        $this->locator = $locator;
        $this->storeManager = $storeManager;
        $this->productOptionsConfig = $productOptionsConfig;
        $this->productOptionsPrice = $productOptionsPrice;
        $this->urlBuilder = $urlBuilder;
        $this->arrayManager = $arrayManager;
        $this->prizeCollection = $prizeCollection;
    }
    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    public function modifyData(array $data)
    {
        $product = $this->locator->getProduct();
        if (!in_array($product->getTypeId(), \Angel\RaffleClient\Model\Raffle::getRaffleProductTypes())){
            return $data;
        }
        $collection = $this->prizeCollection->addFieldToFilter('product_id', $product->getId());
        return array_replace_recursive(
            $data,
            [
                $this->locator->getProduct()->getId() => [
                    static::DATA_SOURCE_DEFAULT => [
                        static::FIELD_ENABLE => 1,
                        static::GRID_PRIZE_NAME => $collection->getData()
                    ]
                ]
            ]
        );
    }

    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    public function modifyMeta(array $meta)
    {
        $product = $this->locator->getProduct();
        if (!in_array($product->getTypeId(), \Angel\RaffleClient\Model\Raffle::getRaffleProductTypes())){
            return $meta;
        }
        if ($product->getTypeId() == \Angel\RaffleClient\Model\Fifty::TYPE_ID){
            $meta = $this->enableTime($meta);
        }
        $this->meta = $meta;
        $this->createCustomOptionsPanel();
        return $this->meta;
    }

    /**
     * Format float number to have two digits after delimiter
     *
     * @param string $path
     * @param array $data
     * @return array
     * @since 101.0.0
     */
    protected function formatPriceByPath($path, array $data)
    {
        $value = $this->arrayManager->get($path, $data);

        if (is_numeric($value)) {
            $data = $this->arrayManager->replace($path, $data, $this->formatPrice($value));
        }

        return $data;
    }

    /**
     * Create "Customizable Options" panel
     *
     * @return $this
     * @since 101.0.0
     */
    protected function createCustomOptionsPanel()
    {
        $this->meta = array_replace_recursive(
            $this->meta,
            [
                static::GROUP_RAFFLE_NAME => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Raffle Information'),
                                'componentType' => Fieldset::NAME,
                                'dataScope' => static::GROUP_RAFFLE_SCOPE,
                                'collapsible' => true,
                                'sortOrder' => $this->getNextGroupSortOrder(
                                    $this->meta,
                                    static::GROUP_RAFFLE_PREVIOUS_NAME,
                                    static::GROUP_RAFFLE_DEFAULT_SORT_ORDER
                                ),
                            ],
                        ],
                    ],
                    'children' => [
                        static::GRID_TYPE_SELECT_NAME => $this->getSelectTypeGridConfig(300)
                    ]
                ]
            ]
        );
        return $this;
    }

    /**
     * Get config for grid for "select" types
     *
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    protected function getSelectTypeGridConfig($sortOrder)
    {
        $options = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'imports' => [
                            'optionId' => '${ $.provider }:${ $.parentScope }.option_id',
                            'optionTypeId' => '${ $.provider }:${ $.parentScope }.option_type_id',
                            'isUseDefault' => '${ $.provider }:${ $.parentScope }.is_use_default'
                        ],
                        'service' => [
                            'template' => 'Magento_Catalog/form/element/helper/custom-option-type-service',
                        ],
                    ],
                ],
            ],
        ];

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'addButtonLabel' => __('Add Value'),
                        'componentType' => DynamicRows::NAME,
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows',
                        'additionalClasses' => 'admin__field-wide',
                        'deleteProperty' => static::FIELD_IS_DELETE,
                        'deleteValue' => '1',
                        'renderDefaultRecord' => false,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'positionProvider' => static::FIELD_SORT_ORDER_NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                            ],
                        ],
                    ],
                    'children' => [
                        static::FIELD_LABEL_NAME => $this->getTitleFieldConfig(
                            10,
                            $this->locator->getProduct()->getStoreId() ? $options : []
                        ),
                        static::FIELD_PRICE_NAME => $this->getPriceFieldConfigForSelectType(20),
                        static::FIELD_PRICE_TYPE_NAME => $this->getPriceTypeFieldConfig(30, ['fit' => true]),
                        static::FIELD_TOTAL_NAME => $this->getTotalFieldConfig(40),
                        static::FIELD_SORT_ORDER_NAME => $this->getPositionFieldConfig(70),
                        static::FIELD_PRIZE_ID_NAME => $this->getPrizeIdFieldConfig(80),
                        static::FIELD_IS_DELETE => $this->getIsDeleteFieldConfig(60)
                    ]
                ]
            ]
        ];
    }

    /**
     * Get currency symbol
     *
     * @return string
     * @since 101.0.0
     */
    protected function getCurrencySymbol()
    {
        return $this->storeManager->getStore()->getBaseCurrency()->getCurrencySymbol();
    }

    /**
     * Get config for hidden field used for removing rows
     *
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    protected function getIsDeleteFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => ActionDelete::NAME,
                        'fit' => true,
                        'sortOrder' => $sortOrder
                    ],
                ],
            ],
        ];
    }

    /**
     * Get config for "Title" fields
     *
     * @param int $sortOrder
     * @param array $options
     * @return array
     * @since 101.0.0
     */
    protected function getTitleFieldConfig($sortOrder, array $options = [])
    {
        return array_replace_recursive(
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Label'),
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'dataScope' => static::FIELD_LABEL_NAME,
                            'dataType' => Text::NAME,
                            'sortOrder' => $sortOrder,
                            'validation' => [
                                'required-entry' => true
                            ],
                        ],
                    ],
                ],
            ],
            $options
        );
    }

    /**
     * Get config for "Price" field
     *
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    protected function getPriceFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Price'),
                        'componentType' => Field::NAME,
                        'component' => 'Magento_Catalog/js/components/custom-options-component',
                        'formElement' => Input::NAME,
                        'dataScope' => static::FIELD_PRICE_NAME,
                        'dataType' => Number::NAME,
                        'addbefore' => $this->getCurrencySymbol(),
                        'addbeforePool' => $this->productOptionsPrice->prefixesToOptionArray(),
                        'sortOrder' => $sortOrder,
                        'validation' => [
                            'validate-zero-or-greater' => true
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get config for "Price" field for select type.
     *
     * @param int $sortOrder
     * @return array
     */
    private function getPriceFieldConfigForSelectType($sortOrder)
    {
        $priceFieldConfig = $this->getPriceFieldConfig($sortOrder);
        $priceFieldConfig['arguments']['data']['config']['template'] = 'Magento_Catalog/form/field';

        return $priceFieldConfig;
    }

    /**
     * Get config for "Price Type" field
     *
     * @param int $sortOrder
     * @param array $config
     * @return array
     * @since 101.0.0
     */
    protected function getPriceTypeFieldConfig($sortOrder, array $config = [])
    {
        return array_replace_recursive(
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Price Type'),
                            'component' => 'Magento_Catalog/js/components/custom-options-price-type',
                            'componentType' => Field::NAME,
                            'formElement' => Select::NAME,
                            'dataScope' => static::FIELD_PRICE_TYPE_NAME,
                            'dataType' => Text::NAME,
                            'sortOrder' => $sortOrder,
                            'options' => $this->productOptionsPrice->toOptionArray(),
                            'imports' => [
                                'priceIndex' => self::FIELD_PRICE_NAME,
                            ],
                        ],
                    ],
                ],
            ],
            $config
        );
    }

    /**
     * Get config for "SKU" field
     *
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    protected function getTotalFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Total'),
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'dataScope' => static::FIELD_TOTAL_NAME,
                        'dataType' => Number::NAME,
                        'sortOrder' => $sortOrder,
                        'validation' => [
                            'required-entry' => true,
                            'validate-number' => true,
                            'validate-digits' => true,
                            'validate-greater-than-zero' => true,
                            'less-than-equals-to' => 10000
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * Get config for hidden field used for sorting
     *
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    protected function getPositionFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'dataScope' => static::FIELD_SORT_ORDER_NAME,
                        'dataType' => Number::NAME,
                        'visible' => false,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
    }

    /**
     * Get config for hidden field used for sorting
     *
     * @param int $sortOrder
     * @return array
     * @since 101.0.0
     */
    protected function getPrizeIdFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'dataScope' => static::FIELD_PRIZE_ID_NAME,
                        'dataType' => Number::NAME,
                        'visible' => false,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
    }

    /**
     * Customise Custom Attribute field
     *
     * @param array $meta
     *
     * @return array
     */
    protected function enableTime(array $meta)
    {
        $fieldCode = 'raffle_start';
        $elementPath = $this->arrayManager->findPath($fieldCode, $meta, null, 'children');
        $containerPath = $this->arrayManager->findPath(static::CONTAINER_PREFIX . $fieldCode, $meta, null, 'children');
        if ($elementPath) {
            $meta = $this->arrayManager->merge(
                $containerPath,
                $meta,
                [
                    'children' => [
                        $fieldCode => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'default' => '',
                                        'options' => [
                                            'dateFormat' > 'Y-m-d',
                                            'timeFormat' => 'HH:mm:ss',
                                            'showsTime' => true
                                        ]
                                    ],
                                ],
                            ],
                        ]
                    ]
                ]
            );
        }

        $fieldCode = 'raffle_end';
        $elementPath = $this->arrayManager->findPath($fieldCode, $meta, null, 'children');
        $containerPath = $this->arrayManager->findPath(static::CONTAINER_PREFIX . $fieldCode, $meta, null, 'children');
//        \Zend_Debug::dump($elementPath);
//        \Zend_Debug::dump($meta['raffle']['children']['container_raffle_end']['children']['raffle_end']['arguments']);
//        die('adsf');
        if ($elementPath) {
            $meta = $this->arrayManager->merge(
                $containerPath,
                $meta,
                [
                    'children' => [
                        $fieldCode => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'default' => '',
                                        'options' => [
                                            'dateFormat' > 'Y-m-d',
                                            'timeFormat' => 'HH:mm:ss',
                                            'showsTime' => true
                                        ]
                                    ],
                                ],
                            ],
                        ]
                    ]
                ]
            );
        }
        return $meta;
    }
}
