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
        // getting all items from cart
        $Total_items = $observer->getCart()->getQuote()->getItemsCount();

        if ($Total_items > 5) { //checking condition
            //$customer = $this->customerSession->getCustomer();


            $customerSessionData = $this->customerSession->getCustomer()->getData();
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/testlog1.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
//            $logger->info('Customer Session Data: ' . print_r($customerSessionData, true));
            $customerName = $customerSessionData['firstname'] . ' ' . $customerSessionData['lastname'];
            $customerEmail = $customerSessionData['email'];

//            $logger->info('Name : ' . $customerName . " and " . $customerEmail);
//            exit();
            $templateId = 'more_than_five_product_email_trigger_template';
            $templateVars = [
                'customer_name' => $customerName,
                'items_count' => $Total_items
            ];
//            $logger->info('Called');
//            $logger->info('Template id ' . print_r($templateVars, true));
//            exit();
            $this->sendEmail($customerEmail, $customerName, $templateId, $templateVars);
        }
    }

    //sending email
    protected function sendEmail($customerEmail, $customerName, $templateId, $templateVars)
    {
//        $senderName = $this->scopeConfig->getValue('trans_email/ident_general/email', ScopeInterface::SCOPE_STORE);
//        $writer2 = new \Zend_Log_Writer_Stream(BP . '/var/log/testlog.log');
//        $logger = new \Zend_Log();
//        $logger->addWriter($writer2);
//        $logger->info('Called');
//        $logger->info(print_r($senderName, true));
//        exit();
        try {
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateVars($templateVars)
                ->setFrom($this->scopeConfig->getValue('trans_email/ident_general/email', ScopeInterface::SCOPE_STORE))
                ->addTo($customerEmail, $customerName)
                ->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
