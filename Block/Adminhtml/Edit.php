<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lovevox\QuestionsAnswers\Block\Adminhtml;

use Magento\Customer\Controller\RegistryConstants;

/**
 * Review edit form.
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Review action pager
     *
     * @var \Lovevox\QuestionsAnswers\Helper\Action\Pager
     */
    protected $_questionActionPager = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Review model factory
     *
     * @var \Lovevox\QuestionsAnswers\Model\CatalogProductQuestion
     */
    protected $_catalogProductQuestionFactory;


    /**
     * Edit constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Lovevox\QuestionsAnswers\Model\CatalogProductQuestionFactory $catalogProductQuestionFactory
     * @param \Lovevox\QuestionsAnswers\Helper\Action\Pager $questionActionPager
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Lovevox\QuestionsAnswers\Model\CatalogProductQuestionFactory $catalogProductQuestionFactory,
        \Lovevox\QuestionsAnswers\Helper\Action\Pager $questionActionPager,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_questionActionPager = $questionActionPager;
        $this->_catalogProductQuestionFactory = $catalogProductQuestionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Initialize edit review
     *
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'Lovevox_QuestionsAnswers';
        $this->_controller = 'adminhtml';

        /** @var $actionPager \Lovevox\QuestionsAnswers\Helper\Action\Pager */
        $actionPager = $this->_questionActionPager;
        $actionPager->setStorageId('question_id');

        $this->buttonList->remove('reset');
        $this->buttonList->update('save', 'label', __('Save Product Question'));
        $this->buttonList->update('delete', 'label', __('Delete'));

        if ($this->getRequest()->getParam($this->_objectId)) {
            $questionData = $this->_catalogProductQuestionFactory->create()->load($this->getRequest()->getParam($this->_objectId));
            $this->_coreRegistry->register('question_data', $questionData);
        }
    }

    /**
     * Prepare layout.
     * Adding save_and_continue button
     *
     * @return $this
     */
    protected function _preparelayout()
    {
        $this->addButton(
            'save_and_edit',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                ]
            ],
            10
        );

        return parent::_prepareLayout();
    }


    /**
     * Return save url for edit form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('adminhtml/*/save', ['_current' => true, 'back' => null]);
    }


    /**
     * Get edit review header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $questionData = $this->_coreRegistry->registry('question_data');
        if ($questionData && $questionData->getId()) {
            return __("Edit Question '%1'", $this->escapeHtml($questionData->getTitle()));
        } else {
            return __('New Question');
        }
    }
}
