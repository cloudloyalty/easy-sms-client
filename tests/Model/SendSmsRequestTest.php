<?php

declare(strict_types=1);

namespace CloudLoyalty\EasySmsClientTests\Model;

use PHPUnit\Framework\TestCase;
use CloudLoyalty\EasySmsClient\Model\SendSmsRequest;

class SendSmsRequestTest extends TestCase
{
    /**
     * @dataProvider dataNullableParameters
     */
    public function testConstructWithNullableParameters(
        SendSmsRequest $request,
        array $expectedHttpQueryParams,
        ?string $expectedConnectId
    ): void {
        $this->assertSame($expectedHttpQueryParams, $request->asHttpQueryParams());
        $this->assertSame($expectedConnectId, $request->getConnectId());
    }

    public function dataNullableParameters(): array
    {
        $httpQueryWithAllParameters = [
            'login' => 'testlogin',
            'psw' => 'testpassword',
            'fmt' => '3',
            'charset' => 'utf-8',
            'cost' => '3',
            'err' => '1',
            'all' => '1',
            'sender' => 'easy-sms.ru',
            'phones' => '71112223344',
            'mes' => 'Hello world!',
            'id' => '12',
        ];

        return [
            'constructor without parameters' => [
                'request' => new SendSmsRequest(),
                'expectedHttpQueryParams' => [],
                'expectedConnectId' => null,
            ],
            'constructor with null parameters' => [
                'request' => new SendSmsRequest([
                    'login' => null,
                    'password' => null,
                    'fmt' => null,
                    'charset' => null,
                    'returnCost' => null,
                    'err' => null,
                    'all' => null,
                    'sender' => null,
                    'connectId' => null,
                    'phone' => null,
                    'message' => null,
                    'id' => null,
                ]),
                'expectedHttpQueryParams' => [],
                'expectedConnectId' => null,
            ],
            'constructor with filled parameters' => [
                'request' => new SendSmsRequest([
                    'login' => 'testlogin',
                    'password' => 'testpassword',
                    'fmt' => '3',
                    'charset' => 'utf-8',
                    'returnCost' => '3',
                    'err' => '1',
                    'all' => '1',
                    'sender' => 'easy-sms.ru',
                    'connectId' => '2115',
                    'phone' => '71112223344',
                    'message' => 'Hello world!',
                    'id' => '12',
                ]),
                'expectedHttpQueryParams' => $httpQueryWithAllParameters,
                'expectedConnectId' => '2115',
            ],
            'request with setter parameters' => [
                'request' => (new SendSmsRequest())
                    ->setLogin('testlogin')
                    ->setPassword('testpassword')
                    ->setFmt('3')
                    ->setCharset('utf-8')
                    ->setReturnCost('3')
                    ->setErr('1')
                    ->setAll('1')
                    ->setSender('easy-sms.ru')
                    ->setConnectId('2115')
                    ->setPhone('71112223344')
                    ->setMessage('Hello world!')
                    ->setId('12'),
                'expectedHttpQueryParams' => $httpQueryWithAllParameters,
                'expectedConnectId' => '2115',
            ],
        ];
    }
}
