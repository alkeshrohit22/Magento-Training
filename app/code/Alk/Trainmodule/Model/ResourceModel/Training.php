<?php

namespace Alk\Trainmodule\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Training extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('Alk_Articals', 'id');
    }
}
