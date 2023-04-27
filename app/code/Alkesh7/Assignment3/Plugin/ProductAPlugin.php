<?php

namespace Alkesh7\Assignment3\Plugin;

use Magento\Catalog\Model\Product;

class ProductAPlugin
{
    public function beforeGetName(Product $subject)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/plugin-sortorder.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('Before Execute From Plugin A');
    }
    public function aroundGetName(Product $subject,  callable $proceed)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/plugin-sortorder.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('Around Before Proceed Execute From Plugin A');

        $return = $proceed();

        $logger->info('Around After Proceed Execute From Plugin A');

        return $return;
    }

    public function afterGetName(Product $subject, $result)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/plugin-sortorder.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('After Execute From Plugin A');
        return $result;
    }
}
