<?php


namespace Angel\RaffleClient\Setup;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;

class InstallData implements InstallDataInterface
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
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'raffle_prefix',
            [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Prefix',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => null,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => true,
                'apply_to' => \Angel\RaffleClient\Model\Raffle::TYPE_ID.','.\Angel\RaffleClient\Model\Fifty::TYPE_ID,
                'system' => 1,
                'group' => 'Raffle',
                'option' => array('values' => array(""))
            ]
        );


        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'total_tickets',
            [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Total Tickets',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => null,
                'searchable' => false,
                'filterable' => false,
                'comparable' => true,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => \Angel\RaffleClient\Model\Raffle::TYPE_ID,
                'system' => 1,
                'group' => 'Raffle',
                'option' => array('values' => array(""))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'raffle_type',
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Generate when new ticket created',
                'input' => 'boolean',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => true,
                'searchable' => false,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => \Angel\RaffleClient\Model\Raffle::TYPE_ID,
                'system' => 1,
                'group' => 'Raffle',
                'option' => array('values' => array(''))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'raffle_start',
            [
                'type' => 'datetime',
                'backend' => '',
                'frontend' => '',
                'label' => 'Start Time',
                'input' => 'date',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
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
                'apply_to' => \Angel\RaffleClient\Model\Fifty::TYPE_ID,
                'system' => 1,
                'group' => 'Raffle',
                'option' => array('values' => array(""))
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'raffle_end',
            [
                'type' => 'datetime',
                'backend' => '',
                'frontend' => '',
                'label' => 'End Time',
                'input' => 'date',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
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
                'apply_to' => \Angel\RaffleClient\Model\Fifty::TYPE_ID,
                'system' => 1,
                'group' => 'Raffle',
                'option' => array('values' => array(""))
            ]
        );


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
                'apply_to' => '',
                'system' => 1,
                'group' => 'Raffle',
                'option' => array('values' => array(""))
            ]
        );

//        $eavSetup->addAttribute(
//            \Magento\Catalog\Model\Product::ENTITY,
//            'minimum_tickets',
//            [
//                'type' => 'int',
//                'backend' => '',
//                'frontend' => '',
//                'label' => 'Minimum Tickets',
//                'input' => 'text',
//                'class' => '',
//                'source' => '',
//                'global' => 1,
//                'visible' => true,
//                'required' => false,
//                'user_defined' => false,
//                'default' => null,
//                'searchable' => false,
//                'filterable' => false,
//                'comparable' => false,
//                'visible_on_front' => false,
//                'used_in_product_listing' => false,
//                'unique' => false,
//                'apply_to' => 'fifty',
//                'system' => 1,
//                'group' => 'Raffle',
//                'option' => array('values' => array(""))
//            ]
//        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'raffle_serial',
            [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Serial',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => null,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => true,
                'apply_to' => \Angel\RaffleClient\Model\Raffle::TYPE_ID.','.\Angel\RaffleClient\Model\Fifty::TYPE_ID,
                'system' => 1,
                'group' => 'Raffle',
                'option' => array('values' => array(""))
            ]
        );

        //associate these attributes with new product type
        $fieldList = [
            'price',
//            'special_price',
//            'special_from_date',
//            'special_to_date',
            'tier_price',
        ];

        // make these attributes applicable to new product type
        $needUpdate = false;
        foreach ($fieldList as $field) {
            $applyTo = explode(
                ',',
                $eavSetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $field, 'apply_to')
            );
            if (!in_array(\Angel\RaffleClient\Model\Raffle::TYPE_ID, $applyTo)) {
                $applyTo[] = \Angel\RaffleClient\Model\Raffle::TYPE_ID;
                $needUpdate = true;
            }
            if (!in_array(\Angel\RaffleClient\Model\Fifty::TYPE_ID, $applyTo)) {
                $applyTo[] = \Angel\RaffleClient\Model\Fifty::TYPE_ID;
                $needUpdate = true;
            }
            if ($needUpdate){
                $eavSetup->updateAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $field,
                    'apply_to',
                    implode(',', $applyTo)
                );
            }
            $needUpdate = false;
        }
        //Your install script
    }
}