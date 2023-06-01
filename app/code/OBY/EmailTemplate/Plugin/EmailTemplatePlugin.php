<?php

namespace OBY\EmailTemplate\Plugin;

use OBY\EmailTemplate\Model\CustomModel;
use Magento\Email\Model\Template;

class EmailTemplatePlugin
{
    private $customModel;

    public function __construct(
        CustomModel $customModel
    ) {
        $this->customModel = $customModel;
    }

    public function afterSetTemplateVars(
        Template $subject,
        $templateVars
    ) {
        $customData = $this->customModel->getCustomData();
        $templateVars['custom_variable'] = 'TEST';//$customData;

        return $templateVars;
    }
}