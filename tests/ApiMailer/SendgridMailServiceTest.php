<?php

namespace Tests\ApiMailer;

use PHPUnit\Framework\TestCase;
use RR\ApiMailer\MailService\SendgridMailService;
use RR\ApiMailer\Message;
use SendGrid;
use SendGrid\Mail\Mail;


class SendgridMailServiceTest extends TestCase
{
    private $message;

    protected function setUp(): void
    {
        $this->message = new Message(
            $subject = 'this is subject',
            $from = 'from@ta.com', $to = ['to@ta.com'],
            $text = 'this is text',
            $html = '<h1>this is HTML content</h1>'
        );
    }

    protected function getInstance($sgClient = null, $sgMail = null)
    {
        $sgClient = $sgClient ?? new SendGrid('SENDGRID_API_KEY');
        $sgMail = $sgMail ?? new Mail();
        return new SendgridMailService($sgClient, $sgMail);
    }

    protected function mockedSendgridClient()
    {
        $sgClient = $this->createMock(SendGrid::class);
        $sgClient->method('send')->willReturn($this->mockedSengridResponse());
        return $sgClient;
    }

    protected function mockedSendgridMail()
    {
        return $this->createMock(Mail::class);
    }

    protected function mockedSengridResponse($statusCode = 200, $body = '')
    {
        $sendgridResponse = $this->createMock(\SendGrid\Response::class);
        $sendgridResponse->method('statusCode')->willReturn($statusCode);
        $sendgridResponse->method('body')->willReturn($body);

        return $sendgridResponse;
    }

    /** @test */
    public function sendgrid_service_instance_created_from_its_static_get_instance_method()
    {
        $this->assertInstanceOf(SendgridMailService::class, SendgridMailService::getInstance());
    }

    /** @test */
    public function an_invalid_message_data_will_cause_an_exception_while_building_the_message_body()
    {
        $this->message->setFrom(null);
        $sg = $this->getInstance();
        $this->expectException(\Exception::class);
        $this->getExpectedExceptionMessage('ehre');
        $sg->send($this->message);
    }

    /** @test */
    public function an_email_will_be_sent_to_sendgrid_external_service()
    {
        $client = $this->createMock(SendGrid::class);
        $client->expects($this->once())->method('send')->willReturn($this->mockedSengridResponse());

        $this->assertTrue($this->getInstance($client)->send($this->message));
    }

    /** @test */
    public function an_exception_when_sendgrid_request_failed()
    {
        $exceptionMessage = "Something wrong happened";
        $badResponse = $this->mockedSengridResponse(500, $exceptionMessage);

        $client = $this->createMock(SendGrid::class);
        $client->expects($this->once())->method('send')->willReturn($badResponse);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($exceptionMessage);
        $this->getInstance($client)->send($this->message);
    }

    /** @test */
    public function an_email_is_build_with_the_message_from_email()
    {
        $sgMail = $this->mockedSendgridMail();
        $sgMail->expects($this->atLeastOnce())->method('setFrom')->with($this->message->getFrom());

        $this->getInstance($this->mockedSendgridClient(), $sgMail)->send($this->message);
    }

    /** @test */
    public function an_email_is_built_with_the_message_to_email()
    {
        $sgMail = $this->mockedSendgridMail();
        $sgMail->expects($this->atLeastOnce())->method('addTo')->with($this->message->getTo()[0]);

        $this->getInstance($this->mockedSendgridClient(), $sgMail)->send($this->message);
    }

    /** @test */
    public function an_email_is_built_with_the_message_subject()
    {
        $sgMail = $this->mockedSendgridMail();
        $sgMail->expects($this->atLeastOnce())->method('setSubject')->with($this->message->getSubject());

        $this->getInstance($this->mockedSendgridClient(), $sgMail)->send($this->message);
    }

    /** @test */
    public function it_returns_the_mail_service_name()
    {
        $this->assertEquals('sendgrid', $this->getInstance()->getServiceName());
    }
}
