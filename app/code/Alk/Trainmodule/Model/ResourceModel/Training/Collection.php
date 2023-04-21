<?php

namespace Alk\Trainmodule\Model\ResourceModel\Training;

use Alk\Trainmodule\Model\Training;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        // First parameter is model class and next is resourceModel class
        $this->_init(Training::class, \Alk\Trainmodule\Model\ResourceModel\Training::class);
    }
}
