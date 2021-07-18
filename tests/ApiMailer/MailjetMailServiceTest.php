<?php

namespace Tests\ApiMailer;

use Mailjet\Client as MailjetClient;
use Mailjet\Response;
use PHPUnit\Framework\TestCase;
use RR\ApiMailer\MailService\MailjetMailService;
use RR\ApiMailer\Message;

class MailjetMailServiceTest extends TestCase
{
    private $message;

    protected function setUp(): void
    {
        $this->message = new Message(
            'this is subject',
            'from@ta.com',
            ['to@ta.com'],
            'this is text',
            '<h1>this is HTML content</h1>'
        );
    }

    protected function getInstance($mgClient = null)
    {
        $mgClient = $mgClient ?? new MailjetClient(
                'MJ_APIKEY_PUBLIC',
                'MJ_APIKEY_PRIVATE',
                false,
                ['version' => 'v3.1']
            );
        return new MailjetMailService($mgClient);
    }

    protected function mockedMailjetResponse($success = true, $body = '')
    {
        $mailjetResponse = $this->createMock(Response::class);
        $mailjetResponse->method('success')->willReturn($success);
        $mailjetResponse->method('getData')->willReturn($body);

        return $mailjetResponse;
    }

    protected function mockedMessage()
    {
        $message = $this->createMock(Message::class);
        $message->expects($this->once())->method('getTo')->willReturn(['email@email.com']);
        $message->expects($this->once())->method('getFrom')->willReturn('email@email.com');
        $message->expects($this->once())->method('getSubject')->willReturn('Subject');
        $message->expects($this->atLeastOnce())->method('getText')->willReturn('Text');
        $message->expects($this->atLeastOnce())->method('getHtml')->willReturn('<strong>HTML</strong>>');

        return $message;
    }

    /** @test */
    public function mailjet_service_instance_created_from_its_static_get_instance_method()
    {
        $this->assertInstanceOf(MailjetMailService::class, MailjetMailService::getInstance());
    }

    /** @test */
    public function a_message_will_be_sent_to_mailjet_external_service()
    {
        $client = $this->createMock(MailjetClient::class);
        $client->expects($this->once())
            ->method('post')
            ->willReturn($this->mockedMailjetResponse());

        $this->assertTrue($this->getInstance($client)->send($this->message));
    }

    /** @test */
    public function an_exception_will_be_thrown_when_mailjet_request_failed()
    {
        $exceptionMessage = "Something wrong happened";

        $client = $this->createMock(MailjetClient::class);
        $client->expects($this->once())
            ->method('post')
            ->willReturn($this->mockedMailjetResponse(false, $exceptionMessage));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($exceptionMessage);
        $this->getInstance($client)->send($this->message);
    }

    /** @test */
    public function the_email_details_are_extracted_from_the_message_after_calling_send_method()
    {
        $client = $this->createMock(MailjetClient::class);
        $client->method('post')->willReturn($this->mockedMailjetResponse());

        $this->getInstance($client)->send($this->mockedMessage());
    }

    /** @test */
    public function it_returns_the_mail_service_name()
    {
       $this->assertEquals('mailjet', $this->getInstance()->getServiceName());
    }
}
