<?php


namespace RR\ApiMailer\MailService;

use Exception;
use \SendGrid\Mail\Mail as SendGridMail;
use \SendGrid as SendGridClient;
use RR\ApiMailer\Message;
use SendGrid\Mail\TypeException;


/**
 * Class SendgridMailService
 * Implementation for the abstract @MailService
 * It will handle all the API email requests to Mailjet
 * @package RR\ApiMailer\MailService
 */
class SendgridMailService extends MailService
{

    /**
     * @var SendGridMail
     */
    protected $sendgridMail;
    /**
     * @var SendGridClient
     */
    protected $sendgridClient;
    /**
     * @var Message
     */
    protected $message;

    /**
     * SendgridMailService constructor.
     * @param SendGridClient $sendgridClient
     * @param SendGridMail $sendgridMail
     */
    public function __construct(SendGridClient $sendgridClient, SendGridMail $sendgridMail)
    {
        $this->sendgridClient = $sendgridClient;
        $this->sendgridMail = $sendgridMail;
    }

    /**
     * return new instance from @SendgridMailService
     * @return MailService
     */
    public static function getInstance(): MailService
    {
        $client = new SendGridClient((string)getenv('SENDGRID_API_KEY'));
        return new static($client, new SendGridMail());
    }


    /**
     * The actual work, to build the email and send it through the API
     * @param Message $message
     * @return bool
     * @throws Exception
     */
    public function send(Message $message): bool
    {
        $this->message = $message;

        $this->build(); // build the service mail content from the message

        $response = $this->sendgridClient->send($this->sendgridMail);
        if ($this->success($response->statusCode())) {
            return true;
        }

        throw new Exception($response->body()); // body will contains the error string, based on the docs
    }

    /**
     * set the email subject
     * @throws TypeException
     */
    protected function buildSubject()
    {
        $this->sendgridMail->setSubject($this->message->getSubject());
    }

    /**
     * set the email contents
     * html & text
     * @throws TypeException
     */
    protected function buildContent()
    {
        if (!empty($this->message->getText())) {
            $this->sendgridMail->addContent('text/plain', $this->message->getText());
        }

        if (!empty($this->message->getHtml())) {
            $this->sendgridMail->addContent('text/html', $this->message->getHtml());
        }
    }

    /**
     * set the From email
     * @throws TypeException
     */
    protected function buildFrom()
    {
        $this->sendgridMail->setFrom($this->message->getFrom());
    }

    /**
     * set the To email(s)
     * @throws TypeException
     */
    protected function buildTo()
    {
        foreach ($this->message->getTo() as $to) {
            $this->sendgridMail->addTo($to);
        }
    }

    /**
     * check if the request is successful
     * @param $httpResponseCode
     * @return bool
     */
    protected function success($httpResponseCode): bool
    {
        // based on SendGrid response status codes.
        return in_array(intval($httpResponseCode), [200, 201, 202, 203, 204]);
    }

}
