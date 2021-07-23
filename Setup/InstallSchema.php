<?php
/**
 * NOTICE OF LICENSE
 * You may not sell, distribute, sub-license, rent, lease or lend complete or portion of software to anyone.
 *
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade to newer
 * versions in the future.
 *
 * @package   RLTSquare_ProductReviewImages
 * @copyright Copyright (c) 2017 RLTSquare (https://www.rltsquare.com)
 * @contacts  support@rltsquare.com
 * @license  See the LICENSE.md file in module root directory
 */

namespace Lovevox\QuestionsAnswers\Setup;

/**
 * Class InstallSchema
 * @package RLTSquare\ProductReviewImages\Setup
 * @author Umar Chaudhry <umarch@rltsquare.com>
 */
class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        //提问表
        $table = $installer->getConnection()->newTable(
            $installer->getTable('catalog_product_question_entity')
        )->addColumn(
            'entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'primary id of this table'
        )->addColumn(
            'store_id', \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT, null, ['nullable' => false], 'store'
        )->addColumn(
            'product_id', \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT, null, ['nullable' => false], 'product_id'
        )->addColumn(
            'customer_id', \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT, null, ['nullable' => false], 'customer_id'
        )->addColumn(
            'name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 64, ['nullable' => false], 'question author name'
        )->addColumn(
            'email', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 32, ['nullable' => false], 'question author email'
        )->addColumn(
            'title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 512, ['nullable' => false], 'question title'
        )->addColumn(
            'is_show', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 1, ['nullable' => false, 'default' => 1], 'question is show'
        )->addColumn(
            'status', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 1, ['nullable' => false, 'default' => 2], 'question is status'
        )->addColumn(
            'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false,'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'created_at'
        )->addColumn(
            'updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null,  ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE], 'updated_at'
        );
        $installer->getConnection()->createTable($table);
        //答案表
        $table = $installer->getConnection()->newTable(
            $installer->getTable('catalog_product_answer_entity')
        )->addColumn(
            'entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'primary id of this table'
        )->addColumn(
            'question_id', \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT, null, ['nullable' => false], 'question id'
        )->addColumn(
            'name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 32, ['nullable' => false], 'answer author name'
        )->addColumn(
            'content', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 512, ['nullable' => false], 'answer content'
        )->addColumn(
            'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false,'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'created_at'
        )->addColumn(
            'updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null,  ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE], 'updated_at'
        )->addForeignKey(
            $installer->getFkName(
                'catalog_product_answer_entity', 'question_id', 'catalog_product_question_entity', 'entity_id'
            ), 'entity_id', $installer->getTable('catalog_product_question_entity'), 'entity_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
