<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lovevox\QuestionsAnswers\Controller\Adminhtml\Questions;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Lovevox\QuestionsAnswers\Controller\Adminhtml\Question as QuestionController;
use Magento\Framework\Controller\ResultFactory;

/**
 * Delete review action.
 */
class Delete extends QuestionController implements HttpPostActionInterface
{
    /**
     * @var \Lovevox\QuestionsAnswers\Model\CatalogProductQuestion
     */
    private $question;

    /**
     * Execute action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $questionId = $this->getRequest()->getParam('id', false);
        try {
            $this->getModel()->aggregate()->delete();

            $this->messageManager->addSuccessMessage(__('The question has been deleted.'));
            $resultRedirect->setPath('questionsanswers/*/');
            return $resultRedirect;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong  deleting this question.'));
        }

        return $resultRedirect->setPath('questionsanswers/*/edit/', ['id' => $questionId]);
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
            return false;
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
