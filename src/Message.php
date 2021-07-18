<?php


namespace RR\ApiMailer;


/**
 * Class Message
 * Simple lightweight class, the main reason is to be the message bearer
 * between the app and the ApiMailer package
 *
 * It contains all the getters methods that will be used by the email service providers
 * like sendgrid and/or mailjet
 *
 * @package RR\ApiMailer
 */
class Message
{
    /**
     * @var string email subject
     */
    protected $subject;
    /**
     * @var string email text body
     */
    protected $text;
    /**
     * @var string email html body
     */
    protected $html;
    /**
     * @var array to emails
     */
    protected $to = [];
    /**
     * @var string from email
     */
    protected $from;

    /**
     * @param string|null $subject
     * @param string|null $from
     * @param array $to
     * @param string|null $text
     * @param string|null $html
     */
    public function __construct(string $subject = null,
                                string $from = null,
                                array $to = [],
                                string $text = null,
                                string $html = null)
    {
        $this->subject = $subject;
        $this->text = $text;
        $this->html = $html;
        $this->to = $to;
        $this->from = $from;
    }

    /**
     * @param $subject
     * @return $this
     */
    public function setSubject($subject): Message
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @param $text
     * @return $this
     */
    public function setText($text): Message
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @param $html
     * @return $this
     */
    public function setHtml($html): Message
    {
        $this->html = $html;
        return $this;
    }

    /**
     * @param $from
     * @return $this
     */
    public function setFrom($from): Message
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @param array $to
     * @return $this
     */
    public function setTo(array $to): Message
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @param $to
     * @return $this
     */
    public function addTo($to): Message
    {
        array_push($this->to, $to);
        return $this;
    }

    /**
     * @return array
     */
    public function getTo(): array
    {
        return $this->to;
    }

    /**
     * @return string|null
     */
    public function getFrom(): ?string
    {
        return $this->from;
    }

    /**
     * @return string|null
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @return string|null
     */
    public function getHtml(): ?string
    {
        return $this->html;
    }

    /**
     * convert the data into associative array
     * @return array
     */
    public function toArray()
    {
        return [
            'From' => $this->getFrom(),
            'Subject' => $this->getSubject(),
            'To' => $this->getTo(),
            'Text' => $this->getText(),
            'Html' => $this->getHtml()
        ];
    }
}
