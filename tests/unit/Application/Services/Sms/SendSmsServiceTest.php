<?php

namespace Sms\Tests\Application\Services\Sms;

use PHPUnit\Framework\TestCase;
use Sms\Application\Services\Sms\SendSmsRequest;
use Sms\Domain\Model\Sms\MessageText;
use Sms\Domain\Model\Sms\MobilePhoneNumber;
use Sms\Application\Services\Sms\Exceptions\SmsApiBadReceiversException;
use Sms\Application\Services\Sms\Exceptions\SmsApiNoCreditException;
use Sms\Application\Services\Sms\Exceptions\SmsApiInvalidApiKeyException;
use Sms\Application\Services\Sms\Exceptions\SmsSendingException;
use Sms\Application\Services\Sms\SendSmsService;
use Sms\Domain\Model\Sms\SmsRepositoryInterface;
use Sms\Application\Services\Sms\SenderProviderInterface;
use Sms\Application\Services\Sms\SendSmsResponse;
use Sms\Domain\Model\Sms\FactorySmsBuilder;
use Sms\Domain\Model\Sms\Sms;
use Sms\Domain\Model\Sms\StatusMessage;

class SendSmsServiceTest extends TestCase
{
    private SendSmsService $service;

    /** @var SmsRepositoryInterface&\PHPUnit\Framework\MockObject\MockObject */
    private SmsRepositoryInterface $repository;

    /** @var SenderProviderInterface&\PHPUnit\Framework\MockObject\MockObject */
    private SenderProviderInterface $senderProvider;

    /** @var FactorySmsBuilder&\PHPUnit\Framework\MockObject\MockObject */
    private FactorySmsBuilder $factory;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(SmsRepositoryInterface::class);
        $this->senderProvider = $this->createMock(SenderProviderInterface::class);
        $this->factory = $this->createMock(FactorySmsBuilder::class);

        $this->service = new SendSmsService(
            $this->repository,
            $this->senderProvider,
            $this->factory
        );
    }

    public function testShouldHandleNoCreditException(): void
    {
        $this->expectException(SmsApiNoCreditException::class);

        $this->senderProvider
            ->method('sendSmsService')
            ->willThrowException(new SmsApiNoCreditException('Insufficient SMS credit'));

        $this->service->execute($this->createRequest());
    }

    public function testShouldHandleBadReceiversException(): void
    {
        $this->expectException(SmsApiBadReceiversException::class);

        $this->senderProvider
            ->method('sendSmsService')
            ->willThrowException(new SmsApiBadReceiversException('Invalid phone number'));

        $this->service->execute($this->createRequest());
    }

    public function testShouldHandleInvalidApiKeyException(): void
    {
        $this->expectException(SmsApiInvalidApiKeyException::class);

        $this->senderProvider
            ->method('sendSmsService')
            ->willThrowException(new SmsApiInvalidApiKeyException('Invalid API key'));

        $this->service->execute($this->createRequest());
    }

    private function createRequest(): SendSmsRequest
    {
        return new SendSmsRequest(
            new MobilePhoneNumber('+33612345678'),
            new MessageText('Hello')
        );
    }

    public function testShouldThrowSmsSendingExceptionOnUnexpectedError(): void
    {
        $this->senderProvider
            ->method('sendSmsService')
            ->willThrowException(new \RuntimeException('Unexpected error'));

        $this->expectException(SmsSendingException::class);
        $this->expectExceptionMessage('Erreur lors de l’envoi du SMS');

        try {
            $this->service->execute($this->createRequest());
            $this->fail('Expected SmsSendingException was not thrown.');
        } catch (SmsSendingException $e) {
            $this->assertSame(0, $e->getCode(), 'Expected exception code 0');
            $this->assertInstanceOf(\RuntimeException::class, $e->getPrevious());
            $this->assertSame('Unexpected error', $e->getPrevious()->getMessage());
            throw $e;
        }
    }

    public function testShouldAddSmsToRepository(): void
    {
        $message = new MessageText('Hello');
        $phoneNumber = new MobilePhoneNumber('+33612345678');
        $sms = $this->createMock(Sms::class);

        $this->factory
            ->expects($this->once())
            ->method('createSms')
            ->with($message, $phoneNumber)
            ->willReturn($sms);

        $this->repository
            ->expects($this->once())
            ->method('add')
            ->with($sms);

        $statusMock = $this->createMock(StatusMessage::class);
        $statusMock->method('getStatusMessage')->willReturn('Envoyé');

        $this->senderProvider
            ->method('sendSmsService')
            ->willReturn($statusMock);

        $request = new SendSmsRequest($phoneNumber, $message);

        $response = $this->service->execute($request);

        $this->assertInstanceOf(SendSmsResponse::class, $response);
    }
}
