<?php

namespace Lovevox\QuestionsAnswers\Controller\Questions;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Store\Model\StoreManagerInterface;

class Add extends \Magento\Framework\App\Action\Action implements HttpGetActionInterface, HttpPostActionInterface
{

    protected $objectManager;
    protected $productFactory;
    protected $logger;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    protected $catalogProductQuestionFactory;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    const EMAIL_TEMPLATE = 'new_customer_question';

    protected $tool;

    const RECIPIENT_EMAIL = 'cs@awbridal.com';

    const FROM = 'sales';

    /**
     * Add constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Lovevox\QuestionsAnswers\Model\CatalogProductQuestionFactory $catalogProductQuestionFactory
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Lovevox\Common\Helper\Tool $tool
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Lovevox\QuestionsAnswers\Model\CatalogProductQuestionFactory $catalogProductQuestionFactory,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Lovevox\Common\Helper\Tool $tool
    )
    {
        parent::__construct($context);
        $this->objectManager = $objectManager;
        $this->logger = $logger;
        $this->productFactory = $productFactory;
        $this->_formKeyValidator = $formKeyValidator;
        $this->catalogProductQuestionFactory = $catalogProductQuestionFactory;
        $this->session = $customerSession;
        $this->storeManager = $storeManager;
        $this->tool = $tool;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost() || !$this->_formKeyValidator->validate($this->getRequest())) {
            exit(json_encode(['code' => 500, 'message' => 'error', 'content' => __('Invalid Form Key. Please refresh the page.')]));
        }

        $product_id = $this->getRequest()->getParam('product_id', 0);
        /** @var \Magento\Catalog\Model\Product $product $product */
        $product = $this->productFactory->create()->load($product_id);

        try {
            if ($product) {
                $customer_id = $this->session->getCustomer()->getId() ?? 0;
                $catalogProductQuestion = $this->catalogProductQuestionFactory->create();
                $catalogProductQuestion->setData('product_id', $product_id);
                $catalogProductQuestion->setData('customer_id', $customer_id);
                $catalogProductQuestion->setData('store_id', $this->storeManager->getStore()->getId());
                $catalogProductQuestion->setData('name', $this->getRequest()->getParam('name'));
                $catalogProductQuestion->setData('email', $this->getRequest()->getParam('email'));
                $catalogProductQuestion->setData('title', $this->getRequest()->getParam('question'));
                $catalogProductQuestion->setData('is_show', $this->getRequest()->getParam('is_show', 0));
                $catalogProductQuestion->save();

                //模板变量
                $vars = [
                    "name" => $this->getRequest()->getParam('name'),
                    "email" => $this->getRequest()->getParam('email'),
                    "question" => $this->getRequest()->getParam('question'),
                ];
                $subject = __('Question from %1', $product->getName());
                $this->tool->sendMail(self::EMAIL_TEMPLATE, [self::RECIPIENT_EMAIL], self::FROM, [], $vars, $subject);
            }
            exit(json_encode(['code' => 200, 'message' => 'success']));
        } catch (\Exception $exception) {
            $this->logger->info('add question info =====>' . $exception->getMessage());
            exit(json_encode(['code' => 500, 'message' => 'error']));
        }
    }


}
