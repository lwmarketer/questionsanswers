<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lovevox\QuestionsAnswers\Controller\Adminhtml\Questions;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Lovevox\QuestionsAnswers\Controller\Adminhtml\Question as ProductController;
use Magento\Framework\Controller\ResultFactory;

/**
 * Edit action.
 */
class Edit extends ProductController implements HttpGetActionInterface
{
    /**
     * @var \Lovevox\QuestionsAnswers\Model\CatalogProductQuestion
     */
    private $question;

    /**
     * Execute action.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Lovevox_QuestionsAnswers::product_questionsanswers_all');
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Product Q&A'));
        $resultPage->addContent($resultPage->getLayout()->createBlock(\Lovevox\QuestionsAnswers\Block\Adminhtml\Edit::class));
        return $resultPage;
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        if (parent::_isAllowed()) {
            return true;
        }

        if (!$this->_authorization->isAllowed('Lovevox_QuestionsAnswers::product_questionsanswers_all')) {
            return  false;
        }

        return true;
    }

    /**
     * Returns requested model.
     *
     * @return \Lovevox\QuestionsAnswers\Model\CatalogProductQuestion
     */
    private function getModel(): \Lovevox\QuestionsAnswers\Model\CatalogProductQuestion
    {
        if ($this->question === null) {
            $this->question = $this->catalogProductQuestionFactory->create()
                ->load($this->getRequest()->getParam('id', false));
        }

        return $this->question;
    }
}
