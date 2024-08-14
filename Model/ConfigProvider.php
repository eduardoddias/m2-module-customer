<?php declare(strict_types=1);

namespace Atwix\Customer\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider
{
    const XML_PATH_EMAIL_CONTACT_NAME = 'trans_email/ident_general/name';
    const XML_PATH_EMAIL_CONTACT_EMAIL = 'trans_email/ident_general/email';
    const XML_PATH_EMAIL_SUPPORT_EMAIL = 'trans_email/ident_support/email';

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        protected ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * @return string|null
     */
    public function getGeneralContactName(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_EMAIL_CONTACT_NAME, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string|null
     */
    public function getGeneralContactEmail(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_EMAIL_CONTACT_EMAIL, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string|null
     */
    public function getCustomerSupportEmail(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_EMAIL_SUPPORT_EMAIL, ScopeInterface::SCOPE_STORE);
    }
}
