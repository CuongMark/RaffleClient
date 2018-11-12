<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Angel\RaffleClient\Model\Ticket;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Product status functionality model
 *
 * @api
 * @since 100.0.2
 */
class Status extends AbstractSource implements SourceInterface, OptionSourceInterface
{
    /**#@+
     * Tickets Status values
     */
    const CHECKED = 1;
    const NOT_CHECKED = 0;
    const WAIT = 2;
    const PAID = 3;
    const WON = 4;
    const LOSE = 5;
    const FALSE = 6;
    const TRASH = 7;

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::CHECKED => __('Checked'),
            self::NOT_CHECKED => __('Not Checked'),
            self::WAIT => __('Wait'),
            self::PAID => __('Paid'),
            self::WON => __('Won'),
            self::LOSE => __('Lose'),
            self::FALSE => __('False'),
            self::TRASH => __('Trash')
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Retrieve option text by option value
     *
     * @param string $optionId
     * @return string
     */
    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();

        return isset($options[$optionId]) ? $options[$optionId] : null;
    }


}
