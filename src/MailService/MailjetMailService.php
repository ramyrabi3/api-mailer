<?php


namespace RR\ApiMailer\MailService;

use Exception;
use Mailjet\Resources;
use Mailjet\Client as MailjetClient;
use RR\ApiMailer\Message;


/**
 * Class MailjetMailService
 * Implementation for the abstract @MailService
 * It will handle all the API email requests to Mailjet
 * @package RR\ApiMailer\MailService
 */
class MailjetMailService extends MailService
{

    protected $mailjetMessage = [];
    protected $mailjetClient;
    protected $message;

    /**
     * MailjetMailService constructor.
     * @param MailjetClient $mailjetClient
     */
    public function __construct(MailjetClient $mailjetClient)
    {
        $this->mailjetClient = $mailjetClient;
    }

    /**
     * return new instance from the @MailjetMailService
     * @return MailjetMailService
     */
    public static function getInstance(): MailService
    {
        $client = new MailjetClient(
            getenv('MJ_APIKEY_PUBLIC'),
            getenv('MJ_APIKEY_PRIVATE'),
            true,
            ['version' => 'v3.1']
        );
        return new static($client);
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

        $this->build();

        $response = $this->mailjetClient->post(Resources::$Email, $this->emailMessageBody());
        if ($response->success()) {
            return true;
        }
        throw new Exception($response->getData());
    }


    /**
     * set the email subject
     */
    protected function buildSubject()
    {
        $this->mailjetMessage['Subject'] = $this->message->getSubject();
    }

    /**
     * set the email contents
     * html & text
     */
    protected function buildContent()
    {
        if (!empty($this->message->getText())) {
            $this->mailjetMessage['TextPart'] = $this->message->getText();
        }

        if (!empty($this->message->getHtml())) {
            $this->mailjetMessage['HTMLPart'] = $this->message->getHtml();
        }
    }

    /**
     * set the From email
     */
    protected function buildFrom()
    {
        $this->mailjetMessage['From'] = ['Email' => $this->message->getFrom()];
    }

    /**
     * set the To email(s)
     */
    protected function buildTo()
    {
        $this->mailjetMessage['To'] = []; // initialize the 'To' array
        foreach ($this->message->getTo() as $to) {
            array_push($this->mailjetMessage['To'], ['Email' => $to]);
        }
    }

    /**
     * the actual API body that will be sent to Mailjet
     * @return array
     */
    protected function emailMessageBody(): array
    {
        return [
            'body' => [
                'Messages' => [$this->mailjetMessage]
            ]
        ];
    }

}
