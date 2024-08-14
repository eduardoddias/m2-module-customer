<?php declare(strict_types=1);

namespace Atwix\Customer\Model\CustomerData\Email;

use Atwix\Customer\Model\ConfigProvider;
use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Sender
{
    /**
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param StateInterface $inlineTranslation
     * @param ConfigProvider $helper
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected TransportBuilder $transportBuilder,
        protected StoreManagerInterface $storeManager,
        protected StateInterface $inlineTranslation,
        protected ConfigProvider $helper,
        protected LoggerInterface $logger
    ) {
    }

    /**
     * @param array $customerData
     * @return void
     */
    public function execute(array $customerData): void
    {
        $this->inlineTranslation->suspend();

        try {
            $emailVars = $this->getEmailVars($customerData);

            $transport = $this->transportBuilder
                ->setTemplateIdentifier('customer_data')
                ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => Store::DEFAULT_STORE_ID])
                ->setTemplateVars($emailVars)
                ->setFromByScope([
                    'name' => $this->helper->getGeneralContactName(),
                    'email' => $this->helper->getGeneralContactEmail()
                ])
                ->addTo([$this->helper->getCustomerSupportEmail()])
                ->getTransport();

            $transport->sendMessage();
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        } finally {
            $this->inlineTranslation->resume();
        }
    }

    /**
     * @param array $customerData
     * @return array
     * @throws Exception
     */
    private function getEmailVars(array $customerData): array
    {
        if (empty($customerData['email'])) {
            throw new Exception('Invalid e-mail address.');
        }
        return [
            'store_name' => $this->storeManager->getStore()->getName(),
            'first_name' => $customerData['first_name'] ?? '',
            'last_name' => $customerData['last_name'] ?? '',
            'email' => $customerData['email']
        ];
    }
}
