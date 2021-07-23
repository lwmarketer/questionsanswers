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

namespace Lovevox\QuestionsAnswers\Model;

/**
 * Class ReviewMedia
 *
 * @package RLTSquare\ProductReviewImages\Model
 * @author Umar Chaudhry <umarch@rltsquare.com>
 */
class CatalogProductAnswer extends \Magento\Framework\Model\AbstractModel
{
    /**
     * constructor
     *
     */
    protected function _construct()
    {
        $this->_init('Lovevox\QuestionsAnswers\Model\ResourceModel\CatalogProductAnswerEntity');
    }
}
