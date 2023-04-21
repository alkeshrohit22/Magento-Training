<?php

namespace Alk\Trainmodule\Model;

use Magento\Framework\Model\AbstractModel;

class Training extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Alk\Trainmodule\Model\ResourceModel\Training::class);
    }
}
