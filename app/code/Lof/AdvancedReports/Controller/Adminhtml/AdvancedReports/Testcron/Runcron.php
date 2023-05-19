<?php

namespace Lof\AdvancedReports\Controller\Adminhtml\AdvancedReports\Testcron;

use Magento\Backend\App\Action\Context;

class Runcron extends \Magento\Backend\App\Action
{
    protected $_crontab;

    public function __construct(Context $context, \Lof\AdvancedReports\Cron\ScheduledSendExports $cronTab)
    {
        $this->_crontab = $cronTab;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $observer = $this->_crontab->execute();
        $this->messageManager->addSuccess(__('The advanced report cron job was processed.'));
        return $resultRedirect->setPath('*/*/');
    }
}
