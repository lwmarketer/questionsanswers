<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lovevox\QuestionsAnswers\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;

/**
 * Reviews admin controller.
 */
abstract class Question extends Action
{
    /**
     * Authorization resource
     */
    public const ADMIN_RESOURCE = 'Lovevox_QuestionsAnswers::product_questionsanswers_all';

    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    protected $_publicActions = ['edit'];

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Review model factory
     *
     * @var \Lovevox\QuestionsAnswers\Model\CatalogProductQuestionFactory
     */
    protected $catalogProductQuestionFactory;


    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Lovevox\QuestionsAnswers\Model\CatalogProductQuestionFactory $catalogProductQuestionFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        \Lovevox\QuestionsAnswers\Model\CatalogProductQuestionFactory $catalogProductQuestionFactory
    ){
        $this->coreRegistry = $coreRegistry;
        $this->catalogProductQuestionFactory = $catalogProductQuestionFactory;
        parent::__construct($context);
    }
}
