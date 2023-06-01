<?php

namespace OBY\EmailTemplate\Model\ResourceModel\CustomData;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use OBY\EmailTemplate\Model\CustomModel;
use OBY\EmailTemplate\Model\ResourceModel\CustomData as CustomDataResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            CustomModel::class,
            CustomDataResourceModel::class
        );
    }
}
