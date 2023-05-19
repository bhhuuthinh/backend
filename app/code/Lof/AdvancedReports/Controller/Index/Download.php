<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.Landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_AdvancedReports
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.Landofcoder.com/)
 * @license    http://www.Landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\AdvancedReports\Controller\Index;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;

class Download extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * Enable/disable configuration
     */
    const XML_PATH_EMAIL_COPY_FOLDER = 'scheduled_email_settings/copy_folder';

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Lof\AdvancedReports\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Download constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Lof\AdvancedReports\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Lof\AdvancedReports\Helper\Data $dataHelper
    )
    {
        parent::__construct($context);
        $this->_fileFactory = $fileFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_dataHelper = $dataHelper;
    }

    /**
     * index action
     */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        $filename = $this->getRequest()->getParam('f');
        $filename = base64_decode($filename);
        $copy_folder = $this->_dataHelper->getConfig(self::XML_PATH_EMAIL_COPY_FOLDER);
        $copy_folder = str_replace("/", DIRECTORY_SEPARATOR, $copy_folder);
        $filepath = $copy_folder;

        if ($filename) {
            try {
                $content['type'] = 'filename';
                $content['value'] = $filepath . $filename;
                return $this->_fileFactory->create($filename, $content, DirectoryList::VAR_DIR);

            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultForward->forward('noroute');
            }
        } else {
            $this->messageManager->addError($filepath . __(' not found'));
            return $resultForward->forward('noroute');
        }
    }

}
