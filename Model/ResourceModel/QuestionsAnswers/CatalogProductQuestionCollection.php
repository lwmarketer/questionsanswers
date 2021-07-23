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

namespace Lovevox\QuestionsAnswers\Model\ResourceModel\QuestionsAnswers;

use Magento\Framework\DB\Select;
use Magento\Review\Model\ResourceModel\Review\Product\Collection;

/**
 * Class Collection
 *
 * @package RLTSquare\ProductReviewImages\Model\ResourceModel\ReviewMedia
 * @author Umar Chaudhry <umarch@rltsquare.com>
 */
class CatalogProductQuestionCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{


    /**
     * Add store data flag
     *
     * @var bool
     */
    protected $_addStoreDataFlag = false;



    /**
     * Add stores data
     *
     * @return $this
     */
    public function addStoreData()
    {
        $this->_addStoreDataFlag = true;
        return $this;
    }

    /**
     * Add entity filter
     *
     * @param int $entityId
     * @return $this
     */
    public function addEntityFilter($entityId)
    {
        $this->getSelect()->where('entity_id = ?', $entityId);
        return $this;
    }


    /**
     * Add status filter
     *
     * @param int $status
     * @return $this
     */
    public function addStatusFilter($status)
    {
        $this->getSelect()->where('status = ?', $status);
        return $this;
    }

    /**
     * Add customer filter
     *
     * @param int $customerId
     * @return $this
     */
    public function addCustomerFilter($customerId)
    {
        $this->getSelect()->where('name = ?', $customerId);
        return $this;
    }

    /**
     * Get result sorted ids
     *
     * @return array
     */
    public function getResultingIds()
    {
        $idsSelect = clone $this->getSelect();

        $data = $this->getConnection()
            ->fetchAll(
                $idsSelect
                    ->reset(Select::LIMIT_COUNT)
                    ->reset(Select::LIMIT_OFFSET)
                    ->columns('entity_id')
            );

        return array_map(
            function ($value) {
                return $value['entity_id'];
            },
            $data
        );

    }

    /**
     * constructor
     *
     */
    protected function _construct()
    {
        $this->_init('Lovevox\QuestionsAnswers\Model\CatalogProductQuestion', 'Lovevox\QuestionsAnswers\Model\ResourceModel\CatalogProductQuestionEntity');
    }
}
