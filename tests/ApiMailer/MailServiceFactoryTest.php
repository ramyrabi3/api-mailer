<?php

namespace Tests\ApiMailer;

use PHPUnit\Framework\TestCase;
use RR\ApiMailer\MailService\MailjetMailService;
use RR\ApiMailer\MailService\MailServiceFactory;
use RR\ApiMailer\MailService\SendgridMailService;
use RR\ApiMailer\Message;

class MailServiceFactoryTest extends TestCase
{

    protected $message;

    protected function setUp(): void
    {
        $this->message = new Message();
    }

    /** @test */
    public function an_exception_if_the_service_is_not_implemented_yet()
    {
        $service = 'fakeservice';
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches("/${service}/");
        MailServiceFactory::create($service);
    }

    /** @test */
    public function it_returns_the_concrete_class_of_the_mail_service_required_sendgrid()
    {
        $service = 'sendgrid';
        $this->assertInstanceOf(SendgridMailService::class, MailServiceFactory::create($service));
    }

    /** @test */
    public function it_returns_the_concrete_class_of_the_mail_service_required_mailjet()
    {
        $service = 'mailjet';
        $this->assertInstanceOf(MailjetMailService::class, MailServiceFactory::create($service));
    }
}
