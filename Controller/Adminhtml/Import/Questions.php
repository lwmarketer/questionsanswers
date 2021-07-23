<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.172
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */


namespace Lovevox\QuestionsAnswers\Controller\Adminhtml\Import;

use Magento\Review\Model\ResourceModel\Rating\Collection as RatingCollection;

class Questions extends \Magento\Backend\App\Action
{
    protected $objectManager;
    protected $logger;
    protected $resultJsonFactory;

    /**
     * File Uploader factory.
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $fileUploaderFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
     */
    protected $_mediaDirectory;


    private $headers = ['SKU', 'Question', 'Customer Name', 'Email', 'Is Private', 'Answer', 'Status', 'Date', 'Reply by'];

    private $productCollectionFactory;

    /**
     * Question model
     *
     * @var \Lovevox\QuestionsAnswers\Model\CatalogProductQuestionFactory
     */
    protected $_questionFactory;

    /**
     * Answer model
     *
     * @var \Lovevox\QuestionsAnswers\Model\CatalogProductAnswerFactory
     */
    protected $_answerFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Questions constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Lovevox\QuestionsAnswers\Model\CatalogProductAnswerFactory $catalogProductAnswerFactory
     * @param \Lovevox\QuestionsAnswers\Model\CatalogProductQuestionFactory $catalogProductQuestionFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Lovevox\QuestionsAnswers\Model\CatalogProductAnswerFactory $catalogProductAnswerFactory,
        \Lovevox\QuestionsAnswers\Model\CatalogProductQuestionFactory $catalogProductQuestionFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        $this->objectManager = $objectManager;
        $this->logger = $logger;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productCollectionFactory = $productCollectionFactory->create();
        $this->_storeManager = $storeManager;
        $this->_questionFactory = $catalogProductQuestionFactory;
        $this->_answerFactory = $catalogProductAnswerFactory;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->filesystem = $filesystem;
        parent::__construct($context);
    }

    /**
     * Ajax action for inline translation
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        /** @var \Magento\MediaStorage\Model\File\Uploader $uploader */
        $uploader = $this->fileUploaderFactory->create(['fileId' => 'file']);
        $uploader->setAllowedExtensions(['csv']);
        $uploader->setAllowRenameFiles(true);
        $path = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)
                ->getAbsolutePath() . '/import';
        if (!file_exists($path)) {
            mkdir($path, 0777);
        }
        try {
            $result = $uploader->save($path);
            $fullPath = $result['path'] . '/' . $result['file'];
            $sucessNum = $faileNum = 0;
            $file = new \Magento\Framework\Filesystem\Driver\File;
            $file->isFile($fullPath);
            $csv = new \Magento\Framework\File\Csv($file);
            $data = $csv->getData($fullPath);

            if (count($data) > 1) {
                $flag = $this->checkHeader($data);
                if (!$flag) {
                    return $resultJson->setData(['code' => 500, 'message' => 'error', 'content' => __('The format of the template is incorrect, please download the template again and import it!')]);
                }

                for ($i = 1; $i < count($data); ++$i) {
                    $item = [];
                    for ($j = 0; $j < count($data[0]); ++$j) {
                        if (isset($data[$i][$j]) && trim($data[$i][$j]) != '') {
                            if (strtolower($data[0][$j]) == 'sku') {
                                /** @var \Magento\Catalog\Model\Product $product */
                                $product = $this->productCollectionFactory->getItemByColumnValue('sku', $data[$i][$j]);
                                if ($product) {
                                    $item['product_id'] = $product->getId();
                                }
                            } else {
                                $str = str_replace(' ', '_', $data[0][$j]);
                                $item[strtolower($str)] = iconv('GBK', 'UTF-8', $data[$i][$j]);
                            }
                        }
                    }

                    if (isset($item['product_id'])) {
                        $created_at = empty($item['date']) ? date('Y-m-d H:i:s', time()) : $item['date'];
                        /** @var \Lovevox\QuestionsAnswers\Model\CatalogProductQuestion $question */
                        $question = $this->_questionFactory->create();
                        $question->setData('store_id', $this->_storeManager->getStore()->getId())
                            ->setData('product_id', $item['product_id'])
                            ->setData('name', $item['customer_name'])
                            ->setData('email', $item['email'])
                            ->setData('title', $item['question'])
                            ->setData('is_show', $item['is_private'])
                            ->setData('status', $item['status'])
                            ->save();

                        $question->aggregate();

                        if (!empty($item['answer'])) {
                            $answer = $this->_answerFactory->create();
                            $answer->setData('question_id', $question->getId());
                            $answer->setData('name', $item['reply_by']);
                            $answer->setData('content', $item['answer']);
                            $answer->save();
                        }
                        //强制修改创建日期
                        if ($question->getId()) {
                            $question->getResource()->getConnection()->update('catalog_product_question_entity', ['created_at' => $created_at], ['entity_id = ?' => $question->getId()]);
                        }
                        $sucessNum++;
                    } else {
                        $faileNum++;
                    }
                }
            }
        } catch (\Exception $e) {
            return $resultJson->setData(['code' => 500, 'message' => 'error', 'content' => __('Import exception, please try again later!')]);
        }
        return $resultJson->setData(['code' => 200, 'message' => 'success', 'content' => __('Import successfully %1 , failed %2', $sucessNum, $faileNum)]);
    }


    /**
     * 验证模板
     * @param $data
     * @return bool
     */
    private function checkHeader($data)
    {
        $flag = true;
        foreach ($data[0] as $k => $v) {
            if ($this->headers[$k] != $v) {
                $flag = false;
                break;
            }
        }
        return $flag;
    }
}
