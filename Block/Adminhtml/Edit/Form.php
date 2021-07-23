<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lovevox\QuestionsAnswers\Block\Adminhtml\Edit;

/**
 * Adminhtml Review Edit Form
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Question data
     *
     * @var \Lovevox\QuestionsAnswers\Helper\Data
     */
    protected $_questionData = null;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * Catalog product factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * Core system store model
     *
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /** @var \Lovevox\QuestionsAnswers\Model\CatalogProductAnswerFactory */
    protected $catalogProductAnswerFactory;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Customer\APi\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Review\Helper\Data $reviewData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Lovevox\QuestionsAnswers\Helper\Data $questionData,
        \Lovevox\QuestionsAnswers\Model\CatalogProductAnswerFactory $catalogProductAnswerFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    )
    {
        $this->_questionData = $questionData;
        $this->customerRepository = $customerRepository;
        $this->_productFactory = $productFactory;
        $this->_systemStore = $systemStore;
        $this->catalogProductAnswerFactory = $catalogProductAnswerFactory;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('wysiwyg_form');
        $this->setTitle(__('Wysiwyg Editor'));
    }

    /**
     * Prepare edit review form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $question = $this->_coreRegistry->registry('question_data');

        $formActionParams = [
            'id' => $this->getRequest()->getParam('id'),
            'ret' => $this->_coreRegistry->registry('ret')
        ];

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl(
                        'questionsanswers/*/save',
                        $formActionParams
                    ),
                    'method' => 'post',
                ],
            ]
        );

        $fieldset = $form->addFieldset(
            'question_details',
            ['legend' => __('Edit'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'name',
            'text',
            [
                'label' => __('Customer Name'),
                'name' => 'name',
                'required' => true,
                'text' => $question->getName()
            ]
        );

        $fieldset->addField(
            'email',
            'text',
            [
                'label' => __('Customer Email'),
                'name' => 'email',
                'required' => true,
                'text' => $question->getEmail()
            ]
        );

        $fieldset->addField(
            'title',
            'text',
            [
                'label' => __('Question'),
                'name' => 'title',
                'required' => true,
                'text' => $question->getTitle()
            ]
        );

        $fieldset->addField(
            'reply_content',
            'editor',
            [
                'label' => __('Answer'),
                'name' => 'reply_content',
                'style' => 'height:24em;',
                'required' => true,
                'config' => $this->_wysiwygConfig->getConfig()
            ]
        );

        $fieldset->addField(
            'is_show',
            'select',
            [
                'label' => __('Is Private'),
                'required' => true,
                'name' => 'is_show',
                'values' => [
                    1 => __('Yes'),
                    0 => __('No')
                ],
            ]

        );

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'required' => true,
                'name' => 'status',
                'values' => $this->_questionData->getQuestionStatuses(),
            ]
        );
        $question->setData('reply_content',$this->getAnswer($question->getId()));
        $form->setUseContainer(true);
        $form->setValues($question->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }


    private function getAnswer($question_id)
    {
        $answer = $this->catalogProductAnswerFactory->create()->load($question_id, 'question_id');
        return $answer->getData('content') ?? '';
    }
}
