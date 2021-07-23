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
class CatalogProductQuestion extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Approved review status code
     */
    const STATUS_Answered = 1;

    /**
     * Pending review status code
     */
    const STATUS_PENDING = 2;

    /**
     * Not Approved review status code
     */
    const STATUS_Published = 3;


    /**
     * Aggregate reviews
     *
     * @return $this
     */
    public function aggregate()
    {
        $this->getResource()->aggregate($this);
        return $this;
    }
    /**
     * constructor
     *
     */
    protected function _construct()
    {
        $this->_init('Lovevox\QuestionsAnswers\Model\ResourceModel\CatalogProductQuestionEntity');
    }
}
