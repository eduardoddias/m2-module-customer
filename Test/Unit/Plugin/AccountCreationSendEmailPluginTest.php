<?php declare(strict_types=1);

namespace Atwix\Customer\Test\Unit\Plugin;

use Atwix\Customer\Model\CustomerData\Email\Sender;
use Atwix\Customer\Plugin\AccountCreationSendEmailPlugin;
use Magento\Customer\Model\AccountManagement;
use Magento\Customer\Model\Data\Customer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AccountCreationSendEmailPluginTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->accountManagement = $this->createMock(AccountManagement::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->emailSenderMock = $this->createMock(Sender::class);
        $this->plugin = new AccountCreationSendEmailPlugin($this->emailSenderMock, $this->loggerMock);
    }

    /**
     * @return void
     */
    public function testAfterCreateAccount()
    {
        $customerMock = $this->createMock(Customer::class);
        $customerMock->method('getFirstname')->willReturn('NameWithWhitespace');
        $customerMock->method('getLastname')->willReturn('Doe');
        $customerMock->method('getEmail')->willReturn('john.doe@example.com');

        $this->loggerMock->expects($this->once())
            ->method('debug')
            ->with('{"first_name":"NameWithWhitespace","last_name":"Doe","email":"john.doe@example.com"}');
        $this->emailSenderMock->expects($this->once())
            ->method('execute')
            ->with([
                'first_name' => 'NameWithWhitespace',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com'
            ]);

        $this->assertSame($customerMock, $this->plugin->afterCreateAccount($this->accountManagement, $customerMock));
    }
}
