<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lof\AdvancedReports\Cron;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Area;

/**
 * Class ScheduledSendExports
 *
 * @package Lof\AdvancedReports\Cron
 */
class ScheduledSendExports {
    const EXP_LOG = 'vesadv_exception.log';
    const SYSTEM_LOG = 'vesadv_system.log';

    const XML_PATH_ENABLE_CRON = 'scheduled_email_settings/enable_cron_tab';

    const XML_PATH_EMAIL_SENDER = 'scheduled_email_settings/email_sender';

    const XML_PATH_NAME_SENDER = 'scheduled_email_settings/name_sender';
    /**
     * Enable/disable configuration
     */
    const XML_PATH_REPORTS = 'scheduled_email_settings/reports_export';
    /**
     * Enable/disable configuration
     */
    const XML_PATH_EMAIL_RECEIPTS = 'scheduled_email_settings/email_recipients';
    /**
     * Enable/disable configuration
     */
    const XML_PATH_EMAIL_PERIOD = 'scheduled_email_settings/period';
    /**
     * Enable/disable configuration
     */
    const XML_PATH_EMAIL_GROUPBY = 'scheduled_email_settings/group_by';
    /**
     * Enable/disable configuration
     */
    const XML_PATH_EMAIL_FILE_FORMART = 'scheduled_email_settings/file_format';
    /**
     * Enable/disable configuration
     */
    const XML_PATH_EMAIL_COPY_FOLDER = 'scheduled_email_settings/copy_folder';
    /**
     * Enable/disable configuration
     */
    const XML_PATH_EMAIL_EMAIL_TEMPLATE = 'scheduled_email_settings/email_template';

    /**
     * Enable/disable configuration
     */
    const XML_PATH_EMAIL_DELETE_OLD_FILE = 'scheduled_email_settings/delete_old_file';
    /**
     * Enable/disable configuration
     */
    const XML_PATH_EMAIL_SUBJECT_PREFIX = 'scheduled_email_settings/email_subject';

    const XML_PATH_ENABLE_DEBUG_FILE = 'scheduled_email_settings/enable_debug_log';

    const XML_PATH_LIMIT = 'scheduled_email_settings/limit';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Resource
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $_dateTime;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor
     */
    protected $_processor;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * @var \Lof\AdvancedReports\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filesystem;

    /**
     * ScheduledSendExports constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface             $storeManager
     * @param \Magento\Framework\App\ResourceConnection              $resource
     * @param \Magento\Framework\Stdlib\DateTime                     $dateTime
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface   $localeDate
     * @param \Magento\Eav\Model\Config                              $eavConfig
     * @param \Magento\Catalog\Model\Indexer\Product\Price\Processor $processor
     * @param \Magento\Framework\View\LayoutInterface                $layout
     * @param \Psr\Log\LoggerInterface                               $logger
     * @param \Magento\Framework\Translate\Inline\StateInterface     $inlineTranslation
     * @param \Lof\AdvancedReports\Model\Mail\UploadTransportBuilder $transportBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface     $scopeConfig
     * @param \Magento\Framework\UrlInterface                        $urlBuilder
     * @param \Magento\Framework\Filesystem                          $filesystem
     * @param \Lof\AdvancedReports\Helper\Data                       $dataHelper
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ResourceConnection $resource,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Indexer\Product\Price\Processor $processor,
        \Magento\Framework\View\LayoutInterface $layout,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Lof\AdvancedReports\Model\Mail\UploadTransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Filesystem $filesystem,
        \Lof\AdvancedReports\Helper\Data $dataHelper
    )
    {
        $this->_storeManager     = $storeManager;
        $this->_resource         = $resource;
        $this->_dateTime         = $dateTime;
        $this->_localeDate       = $localeDate;
        $this->_eavConfig        = $eavConfig;
        $this->_processor        = $processor;
        $this->_dataHelper       = $dataHelper;
        $this->_layout           = $layout;
        $this->logger            = $logger;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->scopeConfig       = $scopeConfig;
        $this->urlBuilder        = $urlBuilder;
        $this->_filesystem       = $filesystem;
    }

    /**
     * Retrieve write connection instance
     *
     * @return bool|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected function _getConnection()
    {
        if (null === $this->_connection) {
            $this->_connection = $this->_resource->getConnection();
        }

        return $this->_connection;
    }

    public function getRootDirPath($path_type = "")
    {
        $path_type = $path_type ? $path_type : DirectoryList::ROOT;

        return $this->_filesystem->getDirectoryRead($path_type)->getAbsolutePath();
    }

    /**
     * Add products to changes list with price which depends on date
     *
     * @return bool|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        if ( ! $this->_dataHelper->getConfig(self::XML_PATH_ENABLE_CRON)) {
            return false;
        }

        $errors       = [];
        $report_types = $this->_dataHelper->getConfig(self::XML_PATH_REPORTS);
        $copy_folder  = $this->_dataHelper->getConfig(self::XML_PATH_EMAIL_COPY_FOLDER);
        $filetype     = $this->_dataHelper->getConfig(self::XML_PATH_EMAIL_FILE_FORMART);
        $filetype     = $filetype ? $filetype : 'csv';
        $period       = $this->_dataHelper->getConfig(self::XML_PATH_EMAIL_PERIOD);
        $period       = $period ? $period : 'today';
        $group_by     = $this->_dataHelper->getConfig(self::XML_PATH_EMAIL_GROUPBY);
        $group_by     = $group_by ? $group_by : 'day';

        $report_types = is_array($report_types) ? $report_types : explode(",", $report_types);
        // check if scheduled generation enabled
        if ( ! $report_types) {
            return;
        }
        //Init filter grid data
        $period_date                       = $this->getPeriodDates($period);
        $filterData                        = [];
        $filterData['filter_from']         = $period_date['from'];
        $filterData['filter_to']           = $period_date['to'];
        $filterData['show_actual_columns'] = 1;
        $filterData['group_by']            = $group_by;
        $filterData['limit']               = $this->_dataHelper->getConfig(self::XML_PATH_LIMIT);
        $filterData['limit']               = $filterData['limit'] ? (int) $filterData['limit'] : 100;
        $filterData['filter_year']         = date("Y", strtotime($period_date['from']));
        $filterData['filter_month']        = date("m", strtotime($period_date['from']));
        $filterData['filter_day']          = "";

        //Init Email Data Information
        $file_name      = "";
        $subject        = "";
        $now            = date("d.m.Y H:i:s");
        $subject_prefix = $this->_dataHelper->getConfig(self::XML_PATH_EMAIL_SUBJECT_PREFIX);
        //Get export files
        foreach ($report_types as $report) {
            //Send emails
            $report_item = $this->_dataHelper->getReportItem($report);
            if ($report_item && isset($report_item['path']) && $report_item['path']) {
                $file_name   = $report_item['value'];
                $report_type = $report_item['path'];
                $subject     = $subject_prefix . " " . $report_item['label'];
                //Step 1: export grid and write into file
                $file      = $this->writeExportFile($file_name, $filetype, $report_item, $filterData);
                $file_name = $file_name . "." . $filetype;
                //Step 2: Send email with attach file
                if ($file) {
                    $this->sendMail($file, $filetype, $file_name, $subject, $now);
                }
            }

        }

        return true;
    }

    /**
     * @param $file
     * @param $filetype
     * @param $filename
     * @param $subject
     * @param $now
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendMail($file, $filetype, $filename, $subject, $now)
    {
        // Get Recepient Email
        $recepientEmail = $this->_dataHelper->getConfig(self::XML_PATH_EMAIL_RECEIPTS);
        $store          = $this->_storeManager->getStore();
        $recipients     = [];
        if (preg_match('/,/', $recepientEmail)) {
            $recepientEmailArr = explode(',', $recepientEmail);
            foreach ($recepientEmailArr as $recepient) {
                $tmp          = ["report" => $recepientEmail];
                $recipients[] = $tmp;
            }
        } else {
            $recipients = ["report" => $recepientEmail];
        }
        $recipient_name = "";
        $log_dir        = $this->getRootDirPath(DirectoryList::LOG);
        // Get Template ID
        $template_id     = $this->_dataHelper->getConfig(self::XML_PATH_EMAIL_EMAIL_TEMPLATE);
        $storeScope      = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $sender_email_id = $this->_dataHelper->getConfig(self::XML_PATH_EMAIL_SENDER);
        $sender_email_id = $sender_email_id ? $sender_email_id : 'general';

        $sender_name  = $this->scopeConfig->getValue('trans_email/ident_' . $sender_email_id . '/name', $storeScope);
        $sender_email = $this->scopeConfig->getValue('trans_email/ident_' . $sender_email_id . '/email', $storeScope);

        $file_url               = $this->urlBuilder->getBaseUrl() . "advancedreports/index/download" . "?f=" . base64_encode($filename);
        $emailTemplateVariables = [
            'recipient_name' => $recipient_name,
            'file_name'      => $filename,
            'file_url'       => $file_url,
            'now'            => $now,
            'recipients'     => $recipients,
        ];
        $send_from              = ['name' => $sender_name, 'email' => $sender_email];
        // SEND EMAIL
        $this->inlineTranslation->suspend();
        if ($recipients && $sender_email) {
            if ($filetype == "CSV") {
                $attach_type = 'application/csv';
            } else {
                $attach_type = 'application/xml';
            }
            foreach ($recipients as $recipient) {
                $this->_transportBuilder->setTemplateIdentifier($template_id)
                                        ->setTemplateOptions(
                                            [
                                                'area'  => Area::AREA_FRONTEND,
                                                'store' => $store->getId(),
                                            ])
                                        ->setTemplateVars($emailTemplateVariables)
                                        ->setFrom($send_from)
                                        ->addTo($recipient)
                                        ->addAttachment(file_get_contents($file), $file, $attach_type); //Attachment goes here.
                try {
                    $transport = $this->_transportBuilder->getTransport();
                    $transport->sendMessage();
                    $this->inlineTranslation->resume();

                    if ($this->_dataHelper->getConfig(self::XML_PATH_ENABLE_DEBUG_FILE)) {
                        $writer = new \Zend\Log\Writer\Stream($log_dir . DIRECTORY_SEPARATOR . self::SYSTEM_LOG);
                        $logger = new \Zend\Log\Logger();
                        $logger->addWriter($writer);
                        $logger->info(__("Mail Sent with attachment file: ") . $filename);
                    }
                } catch (\Exception $e) {
                    //Log Errors
                    if ($this->_dataHelper->getConfig(self::XML_PATH_ENABLE_DEBUG_FILE)) {
                        $writer = new \Zend\Log\Writer\Stream($log_dir . DIRECTORY_SEPARATOR . self::EXP_LOG);
                        $logger = new \Zend\Log\Logger();
                        $logger->addWriter($writer);
                        $logger->info(__("Error when send export emails."));
                        $logger->info($e->getMessage());
                        $logger->info(__("Track Email"));
                        $logger->info($emailTemplateVariables);
                    }
                }
            }
        }

        // END SEND EMAIL
        return;
    }

    /**
     * @param        $blocks
     * @param string $report_type
     * @param string $period_type
     * @param array  $requestData
     * @return $this
     */
    public function _initReportAction($blocks, $report_type = "", $period_type = "", $requestData = [])
    {
        if ( ! is_array($blocks)) {
            $blocks = [$blocks];
        }

        $sort_by = "";
        $dir     = "";

        //$requestData = $this->_filterDates($requestData, array('filter_from', 'filter_to'));
        $requestData['store_ids'] = [];

        if ( ! $requestData['filter_from'] && ! $requestData['filter_to']) {
            $cur_month                  = date("m");
            $cur_year                   = date("Y");
            $requestData['filter_from'] = $cur_month . "/01/" . $cur_year;
            $requestData['filter_to']   = date("m/d/Y");
        }

        $params = new \Magento\Framework\DataObject();

        foreach ($requestData as $key => $value) {
            if ( ! empty($value)) {
                $params->setData($key, $value);
            }
        }

        foreach ($blocks as $block) {
            if ($block) {
                $block->setReportType($report_type);
                $block->setPeriodType($period_type);
                $block->setFilterData($params);
                $block->setCulumnOrder($sort_by);
                $block->setOrderDir($dir);
            }
        }

        return $this;
    }

    /**
     * @param        $folder
     * @param        $file
     * @param        $value
     * @param string $ext
     * @return string
     */
    public function writeFileToCache($folder, $file, $value, $ext = 'csv')
    {
        $this->checkExitsFolder($folder);

        $delete_old_file = $this->_dataHelper->getConfig(self::XML_PATH_EMAIL_DELETE_OLD_FILE);
        if ($delete_old_file) {
            $file_path = $folder . preg_replace('/[^A-Z0-9\._-]/i', '', $file) . '.' . $ext;
        } else {
            $file_path = $folder . preg_replace('/[^A-Z0-9\._-]/i', '', $file) . "_" . time() . '.' . $ext;
        }
        if (file_exists($file_path)) {
            @unlink($file_path);
        }

        @file_put_contents($file_path, $value);
        @chmod($file_path, 0755);

        return $file_path;
    }

    /**
     * @param string $path
     * @return string
     */
    public function checkExitsFolder($path = "")
    {
        if ( ! is_dir($path)) {
            mkdir($path, 0755, true);
        }

        return $path;
    }

    /**
     * @param        $file_name
     * @param string $filetype
     * @param        $report
     * @param        $filterData
     * @return bool|string
     */
    public function writeExportFile($file_name, $filetype = "csv", $report, $filterData)
    {
        //Init Report Grid Block
        $report_key  = $report['value'];
        $report_type = $report['path'];
        $report_type = str_replace("_", "\\", $report_type);
        $report_type = "\\" . $report_type;
        $grid        = $this->_layout->createBlock(
            "Lof\AdvancedReports\Block\Adminhtml\Advancedreport" . $report_type . "\Grid"
        );
        $blocks      = [];
        $blocks[]    = $grid;
        $this->_initReportAction($blocks, $report_key, $filterData['group_by'], $filterData);

        //copy export file to backup folder
        $base_dir    = $this->getRootDirPath();
        $var_dir     = $this->getRootDirPath(DirectoryList::VAR_DIR);
        $copy_folder = $this->_dataHelper->getConfig(self::XML_PATH_EMAIL_COPY_FOLDER);
        $copy_folder = str_replace("/", DIRECTORY_SEPARATOR, $copy_folder);
        $copy_folder = $base_dir . $copy_folder;
        $export_file = [];
        if ($filetype == "csv") {
            $export_file = $grid->getCsvFile();
        } elseif ($filetype == "xml") {
            $export_file = $grid->getExcelFile();
        }
        if ($export_file && file_exists($var_dir . $export_file['value'])) {
            $file_content = @file_get_contents($var_dir . $export_file['value']);
            $file         = $this->writeFileToCache($copy_folder, $file_name, $file_content, $filetype);
            unlink($var_dir . $export_file['value']);

            return $file;
        }

        return false;
    }

    /**
     * @param string $period
     * @return array
     */
    public function getPeriodDates($period = "today")
    {
        $from_date = "";
        $to_date   = "";
        switch ($period) {
            case 'today':
                $from_date = $to_date = date("m/d/Y");
                break;
            case 'yesterday':
                $from_date = $to_date = date('m/d/Y', strtotime("-1 days"));
                break;
            case 'last_7_days':
                $from_date = date('m/d/Y', strtotime("-7 days"));
                $to_date   = date("m/d/Y");
                break;
            case 'last_30_days':
                $from_date = date('m/d/Y', strtotime("-30 days"));
                $to_date   = date("m/d/Y");
                break;
            case 'last_week':
                $start_last_week = strtotime('-2 Sunday');
                $end_last_week   = strtotime("+7 days", $start_last_week);
                $from_date       = date('m/d/Y', $start_last_week);
                $to_date         = date('m/d/Y', $end_last_week);
                break;
            case 'last_business_week':
                $start_week = strtotime("last monday midnight");
                $end_week   = strtotime("+4 days", $start_week);
                $from_date  = date("m/d/Y", $start_week);
                $to_date    = date("m/d/Y", $end_week);
                break;
            case 'this_month':
                $from_date = date('m') . '/01/' . date('Y');
                $to_date   = date("m/d/Y");
                break;
            case 'last_month':
                $last_month_start = strtotime('first day of last month');
                $last_month_end   = strtotime('last day of last month');

                if ( ! $last_month_start) {
                    $from_date = date("m/d/Y", mktime(0, 0, 0, date("m") - 1, 1, date("Y")));
                } else {
                    $from_date = date("m/d/Y", $last_month_start);
                }
                if ( ! $last_month_end) {
                    $to_date = date("m/d/Y", mktime(0, 0, 0, date("m"), 0, date("Y")));
                } else {
                    $to_date = date("m/d/Y", $last_month_end);
                }

                break;
        }

        return ["from" => $from_date, "to" => $to_date];
    }
}
