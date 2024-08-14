<?php declare(strict_types=1);

namespace Atwix\Customer\Plugin;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;

class AccountCreationFirstnameNormalizerPlugin
{
    /**
     * @param AccountManagementInterface $subject
     * @param CustomerInterface $customer
     * @param string|null $password
     * @param string|null $redirectUrl
     * @return array
     */
    public function beforeCreateAccount(
        AccountManagementInterface $subject,
        CustomerInterface $customer,
        string $password = null,
        ?string $redirectUrl = ''
    ): array {
        $customer->setFirstname($this->getFirstnameFormatted($customer));
        return [$customer, $password, $redirectUrl];
    }

    /**
     * @param CustomerInterface $customer
     * @return string
     */
    private function getFirstnameFormatted(CustomerInterface $customer): string
    {
        return str_replace(' ', '', $customer->getFirstname() ?? '');
    }
}
