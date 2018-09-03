<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Angel\RaffleClient\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Form;

class RandomNumber extends AbstractModifier
{
    const GROUP_CONTAINER = 'random_numbers';
    const SORT_ORDER = 4;
    /**
     * @var array
     * @since 101.0.0
     */
    protected $meta = [];

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Angel\RaffleClient\Model\Raffle
     */
    protected $raffle;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        UrlInterface $urlBuilder,
        \Angel\RaffleClient\Model\Raffle $raffle
    ){
        $this->request = $request;
        $this->raffle = $raffle;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    public function modifyData(array $data){
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                static::GROUP_CONTAINER => [
                    'children' => $this->getChildren(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Winning Numbers'),
                                'autoRender' => true,
                                'collapsible' => true,
                                'visible' => true,
                                'opened' => false,
                                'componentType' => Form\Fieldset::NAME,
                                'sortOrder' => static::SORT_ORDER
                            ],
                        ],
                    ],
                ],
            ]
        );
        return $meta;
    }

    /**
     * Retrieve child meta configuration
     *
     * @return array
     */
    protected function getChildren()
    {
        $children = [
            'random_number' => $this->getStockMovements(),
        ];
        return $children;
    }

    protected function getStockMovements(){
        $listingTarget = 'angel_raffleclient_randomnumber_index';
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'autoRender' => true,
                        'componentType' => 'insertListing',
                        'dataScope' => $listingTarget,
                        'externalProvider' => $listingTarget . '.' . $listingTarget . '_data_source',
                        'ns' => $listingTarget,
                        'params' => [
                            'product_id' => $this->request->getParam('id')
                        ],
                        'render_url' => $this->urlBuilder->getUrl('mui/index/render', ['product_id' => $this->request->getParam('id')]),
                        'realTimeLink' => true,
                        'dataLinks' => [
                            'imports' => false,
                            'exports' => true
                        ],
                        'behaviourType' => 'simple',
                        'externalFilterMode' => true,
                        'imports' => [
                            'product_id' => '${ $.provider }:data.entity_id',
                        ],
                        'exports' => [
                            'product_id' => '${ $.externalProvider }:params.entity_id',
                        ]
                    ],
                ],
            ],
        ];
    }

}
