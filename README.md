# API Mailer

API Service Mailer, which allows PHP applications to send emails through API.

Choose between the below list which API mail service you want to use.
It reads the configuration from `environment` using `getenv`.

## Installation

```shell
composer require rr/api-mailer
```

##### [Sendgrid](https://sendgrid.com/)

```shell
# add Sendgird token to the environment  
SENDGRID_API_KEY=[Sendgrid API Token]
```

##### [Mailjet](https://www.mailjet.com/)

```shell
# add Mailjet tokens to the environment.
# Mailjet API version used is v3.1
MJ_APIKEY_PUBLIC=[Mailjet API Public Key]
MJ_APIKEY_PRIVATE=[Mailjet API Secret Key]
```

## Usage

```php
require 'vendor/autoload.php';

$mailer = \RR\ApiMailer\ApiMailer::getMailerInstance('sendgrid'); // sendgrid | mailjet
$message = new \RR\ApiMailer\Message(
    'this is subject', // subject
    'from@gmail.com', // from
    ['to@gmail.com'], // array of recipients
    'this is text', // plain text email body
    '<h1>this is HTML content</h1>' // HTML email body
);

$result = $mailer->send($message);

if ($result === true) {
    echo 'Successfully sent';
} else { // Error
    var_dump($mailer->getError());
}
```
