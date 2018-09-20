<?php


namespace Angel\RaffleClient\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    private $eavSetupFactory;

    /**
     * Constructor
     *
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        if (version_compare($context->getVersion(), "1.0.3", "<")) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'start_pot',
                [
                    'type' => 'decimal',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Start Pot',
                    'input' => 'price',
                    'class' => '',
                    'source' => '',
                    'global' => 1,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => null,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => 'fifty',
                    'system' => 1,
                    'group' => 'Raffle',
                    'option' => array('values' => array(""))
                ]
            );
        }
    }
}
