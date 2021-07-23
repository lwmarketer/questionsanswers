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

namespace Lovevox\QuestionsAnswers\Model\ResourceModel;

/**
 * Class ReviewMedia
 *
 * @package RLTSquare\ProductReviewImages\Model\ResourceModel
 * @author Umar Chaudhry <umarch@rltsquare.com>
 */
class CatalogProductQuestionEntity extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{


    /**
     * constructor
     *
     */
    protected function _construct()
    {
        $this->_init('catalog_product_question_entity', 'entity_id');
    }


    /**
     * Aggregate
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return void
     */
    public function aggregate($object)
    {
        if (!$object->getId() && $object->getEntityId()) {
            $object->load($object->getEntityId());
        }
    }
}
