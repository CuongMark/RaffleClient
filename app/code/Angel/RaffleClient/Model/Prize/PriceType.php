<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Angel\RaffleClient\Model\Raffle;

class PriceType implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @return array|null
     */
    public function toOptionArray()
    {
        return [['value' => 0, 'label' => __('Fixed')], ['value' => 1, 'label' => __('Percent')]];
    }
}
