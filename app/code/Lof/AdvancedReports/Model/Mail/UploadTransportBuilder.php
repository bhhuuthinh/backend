<?php

namespace Lof\AdvancedReports\Model\Mail;

use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;


class UploadTransportBuilder extends TransportBuilder {
    public function __construct(FactoryInterface $templateFactory,
        MessageInterface $message,
        SenderResolverInterface $senderResolver,
        ObjectManagerInterface $objectManager,
        TransportInterfaceFactory $mailTransportFactory) {

        parent::__construct($templateFactory,
            $message,
            $senderResolver,
            $objectManager,
            $mailTransportFactory);
    }

    public function addAttachment($file, $name, $file_type = "application/csv") {
        if (!empty($file) && file_exists($file)) {
            $this->message
            ->createAttachment(
                file_get_contents($file),
                $file_type,
                \Zend_Mime::DISPOSITION_ATTACHMENT,
                \Zend_Mime::ENCODING_BASE64,
                basename($name)
                );
            }

        return $this;
    }

}