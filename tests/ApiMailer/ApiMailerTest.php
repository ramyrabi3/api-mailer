<?php

namespace Tests\ApiMailer;

use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RR\ApiMailer\ApiMailer;
use RR\ApiMailer\MailService\MailService;
use RR\ApiMailer\Message;

class ApiMailerTest extends TestCase
{

    protected $message;

    protected function setUp(): void
    {
        parent::setUp();
        $this->message = new Message(
            $subject = 'this is subject',
            $from = 'from@ta.com',
            $to = ['to@ta.com'],
            $text = 'this is text',
            $html = '<h1>this is HTML content</h1>'
        );
    }

    protected function mockedMailServiceFailed($message = ''): MailService
    {
        $class = $this->getMockForAbstractClass(MailService::class);
        $class->method('send')->with($this->message)->willThrowException(new Exception($message));
        return $class;
    }

    protected function mockedMailServiceSuccess()
    {
        $class = $this->getMockForAbstractClass(MailService::class);
        $class->method('send')->with($this->message)->willReturn(true);
        return $class;
    }

    /** @test */
    public function a_successful_send_message_will_be_sent_to_the_mail_service_class()
    {
        $m = new ApiMailer($this->mockedMailServiceSuccess());
        $this->assertTrue($m->send($this->message));
    }

    /** @test */
    public function a_send_message_will_be_sent_to_the_mail_service_class()
    {
        $exceptionMessage = "Something wrong happened";
        $m = new ApiMailer($this->mockedMailServiceFailed($exceptionMessage));

        $this->assertFalse($m->send($this->message));
        $this->assertEquals($exceptionMessage, $m->getError());
    }

    /** @test */
    public function a_mailer_instance_created_successfully_based_on_service_name_given()
    {
        $service = 'mailjet';
        $mailerInstance = ApiMailer::getMailerInstance($service);
        $this->assertInstanceOf(ApiMailer::class, $mailerInstance);
    }

    /** @test */
    public function a_mailer_can_return_the_service_name_is_being_used()
    {
        $service = 'mailjet';
        $mailerInstance = ApiMailer::getMailerInstance($service);
        $this->assertEquals($service, $mailerInstance->getServiceName());
    }
}
