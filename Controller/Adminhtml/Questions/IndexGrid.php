<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lovevox\QuestionsAnswers\Controller\Adminhtml\Questions;

use Lovevox\QuestionsAnswers\Controller\Adminhtml\Question as ProductController;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;

/**
 * Review grid.
 */
class IndexGrid extends ProductController implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Lovevox\QuestionsAnswers\Model\ResourceModel\QuestionsAnswers\CatalogProductQuestionCollection
     */
    private $collection;

    /**
     * @var \Lovevox\QuestionsAnswers\Model\ResourceModel\QuestionsAnswers\CatalogProductQuestionCollectionFactory
     */
    private $collectionFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param \Lovevox\QuestionsAnswers\Model\CatalogProductQuestionFactory $reviewFactory
     * @param \Lovevox\QuestionsAnswers\Model\ResourceModel\QuestionsAnswers\CatalogProductQuestionCollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        LayoutFactory $layoutFactory,
        Registry $coreRegistry,
        \Lovevox\QuestionsAnswers\Model\CatalogProductQuestionFactory $catalogProductQuestionFactory
    ) {
        parent::__construct($context, $coreRegistry,$catalogProductQuestionFactory);
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * Execute action.
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $layout = $this->layoutFactory->create();
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $resultRaw->setContents($layout->createBlock(\Lovevox\QuestionsAnswers\Block\Adminhtml\Grid::class)->toHtml());
        return $resultRaw;
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lovevox_QuestionsAnswers::product_questionsanswers_all');
    }
}
