<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lovevox\QuestionsAnswers\Controller\Adminhtml\Questions;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Lovevox\QuestionsAnswers\Controller\Adminhtml\Question as QuestionsController;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;

/**
 * Save Review action.
 */
class Save extends QuestionsController implements HttpPostActionInterface
{
    /**
     * @var \Lovevox\QuestionsAnswers\Model\CatalogProductQuestion
     */
    private $question;

    /**
     * @var Session
     */
    private $session;

    /** @var \Lovevox\QuestionsAnswers\Model\CatalogProductAnswerFactory */
    protected $catalogProductAnswerFactory;

    /**
     * @var \Lovevox\Common\Helper\Tool
     */
    protected $tool;

    const FROM = 'support';

    const EMAIL_TEMPLATE = 'new_customer_answer';

    /**
     * Save constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Session $session
     * @param \Lovevox\QuestionsAnswers\Model\CatalogProductQuestionFactory $catalogProductQuestionFactory
     * @param \Lovevox\QuestionsAnswers\Model\CatalogProductAnswerFactory $catalogProductAnswerFactory
     * @param \Lovevox\Common\Helper\Tool $tool
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Session $session,
        \Lovevox\QuestionsAnswers\Model\CatalogProductQuestionFactory $catalogProductQuestionFactory,
        \Lovevox\QuestionsAnswers\Model\CatalogProductAnswerFactory $catalogProductAnswerFactory,
        \Lovevox\Common\Helper\Tool $tool
    )
    {
        parent::__construct($context, $coreRegistry, $catalogProductQuestionFactory);
        $this->session = $session;
        $this->catalogProductAnswerFactory = $catalogProductAnswerFactory;
        $this->tool = $tool;
    }

    /**
     * Save Review action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (($data = $this->getRequest()->getPostValue()) && ($reviewId = $this->getRequest()->getParam('id'))) {
            $question = $this->getModel();

            if (!$question->getId()) {
                $this->messageManager->addErrorMessage(__('The review was removed by another user or does not exist.'));
            } else {
                try {
                    $reply_content = $data['reply_content'];
                    unset($data['relpy_content']);
                    $question->addData($data)->save();
                    $catalogProductAnswer = $this->catalogProductAnswerFactory->create();
                    $answer = $catalogProductAnswer->load($question->getId(), 'question_id');
                    if ($answer->getId()) {
                        $answer->setData('name', $this->session->getUser()->getFirstName());
                        $answer->setData('content', $reply_content);
                        $answer->save();
                    } else {
                        $catalogProductAnswer->setData('question_id', $question->getId());
                        $catalogProductAnswer->setData('name', $this->session->getUser()->getFirstName());
                        $catalogProductAnswer->setData('content', $reply_content);
                        $catalogProductAnswer->save();
                    }
                    $question->aggregate();
                    //模板变量
                    $vars = [
                        "name" => $question->getData('name'),
                        'question' => $question->getData('title'),
                        'submit_date' => $question->getData('created_at'),
                        "reply" => strip_tags(html_entity_decode($reply_content)),
                    ];
                    $subject = __('Response from AW Bridal');
                    $this->tool->sendMail(self::EMAIL_TEMPLATE, [$question->getData('email')], self::FROM, [], $vars, $subject);
                    $this->messageManager->addSuccessMessage(__('You saved the question.'));
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving this question.'));
                }
            }
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('questionsanswers/*/edit', ['id' => $question->getId()]);
                return;
            }
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }
        $resultRedirect->setPath('questionsanswers/*/');
        return $resultRedirect;
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
     * @return  \Lovevox\QuestionsAnswers\Model\CatalogProductQuestion
     */
    private function getModel(): \Lovevox\QuestionsAnswers\Model\CatalogProductQuestion
    {
        if (!$this->question) {
            $this->question = $this->catalogProductQuestionFactory->create()
                ->load($this->getRequest()->getParam('id', false));
        }

        return $this->question;
    }
}
