<?php
declare(strict_types=1);

namespace Mycompany\Emailtrigger\Observer;

use Exception;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface as ObserverInterfaceAlias;
use Magento\Framework\Mail\Template\TransportBuilder as TransportBuilderAlias;
use Magento\Quote\Model\QuoteFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Zend_Log_Exception;

class Triggeremail implements ObserverInterfaceAlias
{
    public function __construct(
        protected TransportBuilderAlias $transportBuilder,
        protected ScopeConfigInterface  $scopeConfig,
        protected QuoteFactory          $quoteFactory,
        protected CustomerSession       $customerSession,
        protected LoggerInterface       $logger,
        protected StoreManagerInterface $_storeManager
    ) {
    }

    public function execute(Observer $observer) : void
    {
        // getting all items from cart
        $Total_items = $observer->getCart()->getQuote()->getItemsCount();

        if ($Total_items > 5) { //checking condition
            $customerSessionData = $this->customerSession->getCustomer()->getData();
            $customerName = $customerSessionData['firstname'] . ' ' . $customerSessionData['lastname'];
            $customerEmail = $customerSessionData['email'];
            $templateId = 'email_trigger_template';
            $templateVars = [
                'customer_name' => $customerName,
                'items_count' => $Total_items
            ];
            try {
                $this->sendEmail($customerEmail, $customerName, $templateId, $templateVars);
            } catch (Zend_Log_Exception $e) {
                $this->logger->critical($e->getTraceAsString());
            }
        }
    }

    /**
     * @throws Zend_Log_Exception
     */
    protected function sendEmail($customerEmail, $customerName, $templateId, $templateVars) : void
    {
        $senderEmail = $this->scopeConfig->getValue('trans_email/ident_general/email', ScopeInterface::SCOPE_STORE);
        $senderName = $this->scopeConfig->getValue('trans_email/ident_general/name', ScopeInterface::SCOPE_STORE);
        try {
            $transport = $this->transportBuilder->setTemplateIdentifier(
                $templateId
            )
                ->setTemplateOptions([
                        'area' => Area::AREA_FRONTEND,
                        'store' => Store::DEFAULT_STORE_ID,
                ])
                ->setTemplateVars($templateVars)
                ->setFromByScope([
                        'name' => $senderName,
                        'email' => $senderEmail,
                ])
                ->addTo($customerEmail, $customerName)
                ->getTransport();
            $transport->sendMessage();
        } catch (Exception $e) {
            $this->logger->critical($e->getTraceAsString());
        }
    }
}
