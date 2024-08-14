<?php declare(strict_types=1);

namespace Atwix\Customer\Plugin;

use Atwix\Customer\Model\CustomerData\Email\Sender;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Data\Customer;
use Psr\Log\LoggerInterface;

class AccountCreationSendEmailPlugin
{
    /**
     * @param Sender $emailSender
     * @param LoggerInterface $logger
     */
    public function __construct(
        protected Sender $emailSender,
        protected LoggerInterface $logger
    ) {
    }

    /**
     * @param AccountManagementInterface $subject
     * @param Customer $result
     * @return Customer
     */
    public function afterCreateAccount(
        AccountManagementInterface $subject,
        Customer $result
    ): Customer {
        $customerData = $this->getCustomerData($result);

        $this->logger->debug(json_encode($customerData));
        $this->emailSender->execute($customerData);

        return $result;
    }

    /**
     * @param Customer $customer
     * @return array
     */
    private function getCustomerData(Customer $customer): array
    {
        return [
            'first_name' => $customer->getFirstname(),
            'last_name' => $customer->getLastname(),
            'email' => $customer->getEmail()
        ];
    }
}
