<?php


namespace RR\ApiMailer;


use Exception;
use RR\ApiMailer\MailService\MailService;
use RR\ApiMailer\MailService\MailServiceFactory;

class ApiMailer
{

    /**
     * @var MailService
     */
    protected $mailService;
    /**
     * @var string
     */
    protected $error;

    /**
     * ApiMailer constructor.
     * @param MailService $mailService
     */
    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Transfer the Message to the concrete @MailService
     * and catch the error in case there were an issue in sending
     * the email
     * @param Message $message
     * @return bool
     */
    public function send(Message $message): bool
    {
        try {
            return $this->mailService->send($message);
        } catch (\Throwable $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * return the service name of the mail service
     * like: mailjet | sendgrid
     * @return string
     */
    public function getServiceName(): string
    {
        return $this->mailService->getServiceName();
    }

    /**
     * Instantiate @ApiMailer class from string $service
     * For simplicity and encapsulation
     * @param string $service
     * @return ApiMailer
     * @throws Exception
     */
    public static function getMailerInstance(string $service): ApiMailer
    {
        return new self(MailServiceFactory::create($service));
    }

    /**
     * The error raised from the @MailService
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @param $error string
     */
    protected function setError(string $error)
    {
        $this->error = $error;
    }

}
