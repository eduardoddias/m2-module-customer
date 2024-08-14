<?php declare(strict_types=1);

namespace Atwix\Customer\Test\Unit\Model\CustomerData\Email;

use Atwix\Customer\Model\ConfigProvider;
use Atwix\Customer\Model\CustomerData\Email\Sender;
use Magento\Framework\App\Area;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class SenderTest extends TestCase
{
    private const CUSTOMER_DATA = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
    ];

    /**
     * @var Sender
     */
    protected Sender $emailSender;

    protected function setUp(): void
    {
        $this->transportBuilderMock = $this->createMock(TransportBuilder::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->stateInterfaceMock = $this->createMock(StateInterface::class);
        $this->configProviderMock = $this->createMock(ConfigProvider::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->emailSender = new Sender(
            $this->transportBuilderMock,
            $this->storeManagerMock,
            $this->stateInterfaceMock,
            $this->configProviderMock,
            $this->loggerMock
        );
    }

    /**
     * @return void
     */
    public function testExecute(): void
    {
        $this->configProviderMock->expects($this->once())
            ->method('getGeneralContactName')->willReturn('Customer Support');

        $this->configProviderMock->expects($this->once())
            ->method('getGeneralContactEmail')->willReturn('contact@example.com');

        $this->configProviderMock->expects($this->once())
            ->method('getCustomerSupportEmail')->willReturn('support@example.com');

        $storeInterface = $this->getMockBuilder(\Magento\Store\Api\Data\StoreInterface::class)
            ->disableOriginalConstructor()->onlyMethods(['getName'])->getMockForAbstractClass();

        $storeInterface->expects($this->once())->method('getName')->willReturn('Magento Store');

        $this->storeManagerMock->expects($this->once())->method('getStore')->willReturn($storeInterface);

        $this->transportBuilderMock->expects($this->once())->method('setTemplateIdentifier')
            ->with('customer_data')->willReturnSelf();

        $this->transportBuilderMock->expects($this->once())->method('setTemplateOptions')
            ->with(['area' => Area::AREA_FRONTEND, 'store' => Store::DEFAULT_STORE_ID])->willReturnSelf();

        $this->transportBuilderMock
            ->expects($this->once())
            ->method('setTemplateVars')
            ->with([
                'store_name' => 'Magento Store',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
            ])
            ->willReturnSelf();

        $this->transportBuilderMock
            ->expects($this->once())
            ->method('setFromByScope')
            ->with([
                'name' => 'Customer Support',
                'email' => 'contact@example.com'
            ])
            ->willReturnSelf();

        $this->transportBuilderMock
            ->expects($this->once())
            ->method('addTo')
            ->willReturnSelf();

        $transportInterface = $this->createMock(TransportInterface::class);

        $transportInterface->expects($this->once())
            ->method('sendMessage')->willReturnSelf();

        $this->transportBuilderMock
            ->expects($this->once())
            ->method('getTransport')
            ->willReturn($transportInterface);

        $this->emailSender->execute(self::CUSTOMER_DATA);
    }

    /**
     * @return void
     */
    public function testExecuteException(): void
    {
        $this->storeManagerMock->expects($this->once())->method('getStore')
            ->willThrowException(new \Exception());
        $this->loggerMock->expects($this->once())->method('error')
            ->willReturnSelf();

        $this->emailSender->execute(self::CUSTOMER_DATA);
    }

}
