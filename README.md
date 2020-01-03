# easy-sms-client

![](https://github.com/cloudloyalty/easy-sms-client/workflows/CI%20test/badge.svg)

PHP client for [easy-sms.ru](https://easy-sms.ru/) API.

Supports sending sms via http protocol.

## Installation

```sh
composer require cloudloyalty/easy-sms-client
```

## Usage example

### Configuration

```php
<?php

use CloudLoyalty\EasySmsClient\EasySmsHttpClient;
use CloudLoyalty\EasySmsClient\Model\BaseOptions;
use GuzzleHttp\Client;
use JMS\Serializer\SerializerBuilder;

$guzzleClient = new Client([
    'base_uri' => 'https://xml.smstec.ru',
    'connect_timeout' => 5,
    'timeout' => 5,
]);

$serializer = SerializerBuilder::create()->build();

// Options in constructor are optional, all parameters can be configured on per-sms basis
$baseOptions = new BaseOptions([
    'login' => 'testlogin',
    'password' => 'testpassword',
    'fmt' => '3',
    'charset' => 'utf-8',
    'returnCost' => '3',
    'err' => '1',
    'all' => '1',
    'sender' => 'easy-sms.ru',
    'connectId' => '2115',
]);

$client = new EasySmsHttpClient($guzzleClient, $serializer, $baseOptions);
```

### Send sms

```php
use CloudLoyalty\EasySmsClient\Exception\BadResponseException;
use CloudLoyalty\EasySmsClient\Exception\TransportException;
use CloudLoyalty\EasySmsClient\Model\SendSmsRequest;

$request = new SendSmsRequest([
    'phone' => '71112223344',
    'message' => 'Hello world!',
    'id' => '12',
]);

try {
    $result = $client->sendSms($request);
} catch (TransportException $e) {
    // http/server errors like 4xx, 5xx
} catch (BadResponseException $e) {
    // Unknown response, deserialization errors
}
```
