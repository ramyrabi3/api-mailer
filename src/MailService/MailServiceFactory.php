<?php


namespace RR\ApiMailer\MailService;


use Exception;
use ReflectionClass;

/**
 * Class MailServiceFactory
 * Factory Design Pattern implementation
 * to facilitate the instantiation of the MailService concrete classes
 * @package RR\ApiMailer\MailService
 */
class MailServiceFactory
{
    /**
     * get instance for the concrete (MailService) class
     * like SendgridMailService & MailjetMailService
     * @param string $service
     * @return MailService
     * @throws Exception
     */
    public static function create(string $service): MailService
    {
        $serviceClass = static::serviceClassName($service);
        if (!class_exists($serviceClass)) {
            throw new Exception("There is no implementation for service :: ${service}");
        }

        return $serviceClass::getInstance();
    }

    /**
     * resolve the class name
     * @param $service
     * @return string
     */
    private static function serviceClassName($service): string
    {
        $service = ucfirst(strtolower($service));
        return static::currentNameSpace() . "\\${service}MailService";
    }

    /**
     * resolve the underling namespace
     * @return string
     */
    private static function currentNameSpace(): string
    {
        return (new ReflectionClass(static::class))->getNamespaceName();
    }

}
