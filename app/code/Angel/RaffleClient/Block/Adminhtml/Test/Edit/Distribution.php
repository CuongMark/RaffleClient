<?php


namespace Angel\RaffleClient\Block\Adminhtml\Test\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Distribution extends GenericButton implements ButtonProviderInterface
{

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [
            'label' => __('RNG Distribution Chart'),
            'class' => 'distribution',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'distribution']],
                'form-role' => 'distribution',
            ],
            'sort_order' => 20,
        ];
        return $data;
    }

    /**
     * Get URL for delete button
     *
     * @return string
     */
    public function getDistributionUrl()
    {
        return $this->getUrl('*/*/distribution', ['test_id' => $this->getModelId()]);
    }
}
