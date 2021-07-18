<?php


namespace RR\ApiMailer\MailService;

use ReflectionClass;
use RR\ApiMailer\Message;

abstract class MailService
{

    /**
     * This function will do the actual work to send the message/email
     * to the external mail service
     * @param Message $message
     * @return bool
     */
    abstract public function send(Message $message): bool;

    /**
     * this will encapsulate the constructor inside it
     * the benefit is to give the ability to each concrete class
     * to be able to inject its dependency, so we can have a unified constructor call
     * @return MailService
     */
    abstract public static function getInstance(): MailService;

    public function getServiceName(): string
    {
        $className = (new ReflectionClass($this))->getShortName();
        // TODO , search for better solution for clean up the work
        return strtolower(str_replace('MailService', '', $className));
    }

    /**
     * The actual work of building the message
     * This is how the email/message will be configured before sending it
     */
    protected function build()
    {
        $this->buildSubject();
        $this->buildFrom();
        $this->buildTo();
        $this->buildContent();
    }

    /**
     *
     */
    protected function buildSubject()
    {

    }

    /**
     *
     */
    protected function buildFrom()
    {

    }

    /**
     *
     */
    protected function buildTo()
    {

    }

    /**
     *
     */
    protected function buildContent()
    {

    }
}
