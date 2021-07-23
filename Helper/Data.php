<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lovevox\QuestionsAnswers\Helper;

/**
 * Default review helper
 *
 * @api
 * @since 100.0.2
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * Filter manager
     *
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filter;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Filter\FilterManager $filter
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Filter\FilterManager $filter
    ) {
        $this->_escaper = $escaper;
        $this->filter = $filter;
        parent::__construct($context);
    }

    /**
     * Get review detail
     *
     * @param string $origDetail
     * @return string
     */
    public function getDetail($origDetail)
    {
        return nl2br($this->filter->truncate($origDetail, ['length' => 50]));
    }

    /**
     * Return short detail info in HTML
     *
     * @param string $origDetail Full detail info
     * @return string
     */
    public function getDetailHtml($origDetail)
    {
        return nl2br($this->filter->truncate($this->_escaper->escapeHtml($origDetail), ['length' => 50]));
    }

     /**
     * Get review statuses with their codes
     *
     * @return array
     */
    public function getQuestionStatuses()
    {
        return [
            \Lovevox\QuestionsAnswers\Model\CatalogProductQuestion::STATUS_Answered => __('Answered'),
            \Lovevox\QuestionsAnswers\Model\CatalogProductQuestion::STATUS_PENDING => __('Pending'),
            \Lovevox\QuestionsAnswers\Model\CatalogProductQuestion::STATUS_Published => __('Published')
        ];
    }

    /**
     * Get review statuses as option array
     *
     * @return array
     */
    public function getQuestionStatusesOptionArray()
    {
        $result = [];
        foreach ($this->getQuestionStatuses() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }

        return $result;
    }
}
