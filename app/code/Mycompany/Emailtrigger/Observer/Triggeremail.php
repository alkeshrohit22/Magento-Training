<?php

namespace Mycompany\Emailtrigger\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface as ObserverInterfaceAlias;
use Magento\Framework\Mail\Template\TransportBuilder as TransportBuilderAlias;
use Magento\Quote\Model\QuoteFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Triggeremail implements ObserverInterfaceAlias
{
    protected $transportBuilder;
    protected $scopeConfig;
    protected $quoteFactory;
    protected $customerSession;
    protected $logger;
    private $_storeManager;

    public function __construct(
        TransportBuilderAlias $transportBuilder,
        ScopeConfigInterface  $scopeConfig,
        QuoteFactory          $quoteFactory,
        CustomerSession       $customerSession,
        LoggerInterface       $logger,
        StoreManagerInterface $_storeManager
    )
    {
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->quoteFactory = $quoteFactory;
        $this->customerSession = $customerSession;
        $this->logger = $logger;
        $this->_storeManager = $_storeManager;
    }

    public function execute(Observer $observer)
    {

//        $writer2 = new \Zend_Log_Writer_Stream(BP . '/var/log/testlog1.log');
//        $logger = new \Zend_Log();
//        $logger->addWriter($writer2);
//        $logger->info('Enter in execute');

        // getting all items from cart
        $Total_items = $observer->getCart()->getQuote()->getItemsCount();

        if ($Total_items > 5) { //checking condition
//            $logger->info("Total items are" . $Total_items);
            $customerSessionData = $this->customerSession->getCustomer()->getData();
            $customerName = $customerSessionData['firstname'] . ' ' . $customerSessionData['lastname'];
            $customerEmail = $customerSessionData['email'];
//            $logger->info($customerName . " " . $customerEmail);
            $templateId = 'more_than_five_product_email_trigger_template';
            $templateVars = [
                'customer_name' => $customerName,
                'items_count' => $Total_items
            ];
            $this->sendEmail($customerEmail, $customerName, $templateId, $templateVars);
        }
    }

    protected function sendEmail($customerEmail, $customerName, $templateId, $templateVars)
    {
        $writer2 = new \Zend_Log_Writer_Stream(BP . '/var/log/testlog2.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer2);
//        $logger->info('Enter in send email function');
//        $logger->info($customerName . " " . $customerEmail);
//        $logger->info("Template ID" . $templateId);
//        $logger->info(print_r($templateVars));
        $senderEmail = $this->scopeConfig->getValue('trans_email/ident_general/email', ScopeInterface::SCOPE_STORE);
        $senderName = $this->scopeConfig->getValue('trans_email/ident_general/name', ScopeInterface::SCOPE_STORE);
        $logger->info("Sender details" . $senderName . " " . $senderEmail);
        exit();
        try {
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateVars($templateVars)
                ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->_storeManager->getStore()->getId()])
                ->setFrom([
                    'email' => $senderEmail,
                    'name' => $senderName
                ])
                ->addTo($customerEmail, $customerName)
                ->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->logger->critical($e->getTraceAsString());
        }
    }
}
