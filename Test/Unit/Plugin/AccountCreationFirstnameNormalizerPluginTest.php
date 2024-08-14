<?php declare(strict_types=1);

namespace Atwix\Customer\Test\Unit\Plugin;

use Atwix\Customer\Plugin\AccountCreationFirstnameNormalizerPlugin;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AccountManagement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AccountCreationFirstnameNormalizerPluginTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->accountManagement = $this->createMock(AccountManagement::class);
        $this->plugin = new AccountCreationFirstnameNormalizerPlugin();
    }

    /**
     * @return void
     */
    public function testBeforeCreateAccount()
    {
        $customerMock = $this->createMock(CustomerInterface::class);
        $customerMock->method('getFirstname')->willReturn('Name With Whitespace');
        $customerMock->expects($this->once())
            ->method('setFirstname')
            ->with('NameWithWhitespace');

        $this->assertEquals(
            [$customerMock, 'password', ''],
            $this->plugin->beforeCreateAccount($this->accountManagement, $customerMock, 'password', '')
        );
    }
}
