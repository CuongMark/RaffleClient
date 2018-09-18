<?php


namespace Angel\RaffleClient\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        //Your install script

        $table_angel_raffleclient_prize = $setup->getConnection()->newTable($setup->getTable('angel_raffleclient_prize'));

        $table_angel_raffleclient_prize->addColumn(
            'prize_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Prize ID'
        );

        $table_angel_raffleclient_prize->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Product Id'
        );

        $table_angel_raffleclient_prize->addColumn(
            'total',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['default' => '0','nullable' => False],
            'Total Tickets'
        );

        $table_angel_raffleclient_prize->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['default' => '0','nullable' => False],
            'Sort Order'
        );

        $table_angel_raffleclient_prize->addColumn(
            'label',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Prize Label'
        );

        $table_angel_raffleclient_prize->addColumn(
            'price_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Price Type'
        );

        $table_angel_raffleclient_prize->addColumn(
            'price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            12,
            ['scale' => 4],
            'price'
        );

        $table_angel_raffleclient_prize->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Raffle Status'
        );

        $table_angel_raffleclient_prize->addForeignKey(
            $setup->getFkName('angel_raffleclient_prize', 'product_id', 'catalog_product_entity', 'entity_id'),
            'product_id',
            $setup->getTable('catalog_product_entity'),
            'entity_id',
            Table::ACTION_CASCADE
        );

        $table_angel_raffleclient_ticket = $setup->getConnection()->newTable($setup->getTable('angel_raffleclient_ticket'));

        $table_angel_raffleclient_ticket->addColumn(
            'ticket_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Entity ID'
        );

        $table_angel_raffleclient_ticket->addColumn(
            'invoice_item_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Invoice Item Id'
        );

        $table_angel_raffleclient_ticket->addColumn(
            'start',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Ticket Start Number'
        );

        $table_angel_raffleclient_ticket->addColumn(
            'end',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Ticket End Number'
        );

        $table_angel_raffleclient_ticket->addColumn(
            'serial',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Ticket Verify Number'
        );

        $table_angel_raffleclient_ticket->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Ticket Status'
        );

        $table_angel_raffleclient_ticket->addForeignKey(
            $setup->getFkName('angel_raffleclient_ticket', 'invoice_item_id', 'sales_invoice_item', 'entity_id'),
            'invoice_item_id',
            $setup->getTable('sales_invoice_item'),
            'entity_id',
            Table::ACTION_CASCADE
        );

        $table_angel_raffleclient_randomnumber = $setup->getConnection()->newTable($setup->getTable('angel_raffleclient_randomnumber'));

        $table_angel_raffleclient_randomnumber->addColumn(
            'randomnumber_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Entity ID'
        );

        $table_angel_raffleclient_randomnumber->addColumn(
            'prize_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Prize Id'
        );

        $table_angel_raffleclient_randomnumber->addColumn(
            'number',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Random Number Generated'
        );

        $table_angel_raffleclient_randomnumber->addColumn(
            'price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            12,
            ['scale' => 4],
            'winning price'
        );

        $table_angel_raffleclient_randomnumber->addColumn(
            'base_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            12,
            ['scale' => 4],
            'base winning price'
        );

//        $table_angel_raffleclient_randomnumber->addForeignKey(
//            $setup->getFkName('angel_raffleclient_randomnumber', 'prize_id', 'angel_raffleclient_prize', 'prize_id'),
//            'prize_id',
//            $setup->getTable('angel_raffleclient_prize'),
//            'prize_id',
//            Table::ACTION_CASCADE
//        );

        $table_angel_raffleclient_transaction = $setup->getConnection()->newTable($setup->getTable('angel_raffleclient_transaction'));

        $table_angel_raffleclient_transaction->addColumn(
            'transaction_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Entity ID'
        );

        $table_angel_raffleclient_transaction->addColumn(
            'ticket_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Ticket Id'
        );

        $table_angel_raffleclient_transaction->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            [],
            'Transaction created time'
        );

        $table_angel_raffleclient_transaction->addColumn(
            'code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Transaction Code'
        );

        $table_angel_raffleclient_transaction->addColumn(
            'price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            12,
            ['scale' => 4],
            'price'
        );

        $table_angel_raffleclient_transaction->addForeignKey(
            $setup->getFkName('angel_raffleclient_transaction', 'ticket_id', 'angel_raffleclient_ticket', 'ticket_id'),
            'ticket_id',
            $setup->getTable('angel_raffleclient_ticket'),
            'ticket_id',
            Table::ACTION_CASCADE
        );

        $setup->getConnection()->createTable($table_angel_raffleclient_transaction);

        $setup->getConnection()->createTable($table_angel_raffleclient_randomnumber);

        $setup->getConnection()->createTable($table_angel_raffleclient_ticket);

        $setup->getConnection()->createTable($table_angel_raffleclient_prize);
    }
}
