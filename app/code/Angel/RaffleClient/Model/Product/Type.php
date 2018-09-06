<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Angel\RaffleClient\Model\Product;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Product type model
 *
 * @api
 * @since 100.0.2
 */
class Type implements OptionSourceInterface
{
    /**
     * Get product type labels array
     *
     * @return array
     */
    public function getOptionArray()
    {
        return [\Angel\RaffleClient\Model\Raffle::TYPE_ID=> __('Raffle'), \Angel\RaffleClient\Model\Fifty::TYPE_ID => __('50/50 Raffle')];
    }

    /**
     * Get product type labels array with empty value
     *
     * @return array
     */
    public function getAllOption()
    {
        $options = $this->getOptionArray();
        array_unshift($options, ['value' => '', 'label' => '']);
        return $options;
    }

    /**
     * Get product type labels array with empty value for option element
     *
     * @return array
     */
    public function getAllOptions()
    {
        $res = $this->getOptions();
        array_unshift($res, ['value' => '', 'label' => '']);
        return $res;
    }

    /**
     * Get product type labels array for option element
     *
     * @return array
     */
    public function getOptions()
    {
        $res = [];
        foreach ($this->getOptionArray() as $index => $value) {
            $res[] = ['value' => $index, 'label' => $value];
        }
        return $res;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }
}
