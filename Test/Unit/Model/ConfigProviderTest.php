<?php declare(strict_types=1);

namespace Atwix\Customer\Test\Unit\Model;

use Atwix\Customer\Model\ConfigProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->dataHelper = new ConfigProvider($this->scopeConfigMock);
    }

    /**
     * @return void
     */
    public function testGetGeneralContactName()
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::XML_PATH_EMAIL_CONTACT_NAME, ScopeInterface::SCOPE_STORE)
            ->willReturn('John Doe');

        $this->assertEquals('John Doe', $this->dataHelper->getGeneralContactName());
    }

    /**
     * @return void
     */
    public function testGetGeneralContactEmail()
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::XML_PATH_EMAIL_CONTACT_EMAIL, ScopeInterface::SCOPE_STORE)
            ->willReturn('test@example.com');

        $this->assertEquals('test@example.com', $this->dataHelper->getGeneralContactEmail());
    }

    public function testGetCustomerSupportEmail()
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::XML_PATH_EMAIL_SUPPORT_EMAIL, ScopeInterface::SCOPE_STORE)
            ->willReturn('support@example.com');

        $this->assertEquals('support@example.com', $this->dataHelper->getCustomerSupportEmail());
    }
}
