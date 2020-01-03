<?php

declare(strict_types=1);

namespace CloudLoyalty\EasySmsClientTests\Client;

use CloudLoyalty\EasySmsClient\EasySmsHttpClient;
use CloudLoyalty\EasySmsClient\Exception\BadResponseException;
use CloudLoyalty\EasySmsClient\Exception\TransportException;
use CloudLoyalty\EasySmsClient\Model\BaseOptions;
use CloudLoyalty\EasySmsClient\Model\ResponsePhone;
use CloudLoyalty\EasySmsClient\Model\SendSmsRequest;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;

class EasySmsHttpClientTest extends TestCase
{
    /** @var MockHandler */
    private $mockHandler;

    /** @var HandlerStack */
    private $handlerStack;

    /** @var Client */
    private $guzzleClient;

    /** @var SerializerInterface */
    private $serializer;

    /** @var EasySmsHttpClient */
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockHandler = new MockHandler();
        $this->handlerStack = HandlerStack::create($this->mockHandler);
        $this->guzzleClient = new Client([
            'base_uri' => 'https://xml.smstec.ru',
            'connect_timeout' => 5,
            'timeout' => 5,
            'handler' => $this->handlerStack,
        ]);

        $this->serializer = SerializerBuilder::create()->build();

        $this->client = new EasySmsHttpClient(
            $this->guzzleClient,
            $this->serializer
        );
    }

    private function createFullSmsRequest(): SendSmsRequest
    {
        return (new SendSmsRequest())
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
            ->setId('12');
    }

    private function createBaseOptions(): BaseOptions
    {
        return (new BaseOptions())
            ->setLogin('testlogin')
            ->setPassword('testpassword')
            ->setFmt('3')
            ->setCharset('utf-8')
            ->setReturnCost('3')
            ->setErr('1')
            ->setAll('1')
            ->setSender('easy-sms.ru')
            ->setConnectId('2115');
    }

    /**
     * @dataProvider dataSendSmsHappyPathWithDifferentConfigurations
     */
    public function testSendSmsWhenHappyPathWithDifferentConfigurations(
        SendSmsRequest $request,
        ?BaseOptions $baseOptions
    ): void {
        $calledSend = false;

        $this->mockHandler->append(function (Request $request, array $options) use (&$calledSend) {
            $uri = $request->getUri();
            $this->assertEquals('https', $uri->getScheme());
            $this->assertEquals('xml.smstec.ru', $uri->getHost());
            $this->assertEquals('/api/v1/smsc/2115/send_sms', $uri->getPath());
            $this->assertEquals('GET', $request->getMethod());

            parse_str($uri->getQuery(), $query);
            $this->assertEquals('testlogin', $query['login']);
            $this->assertEquals('testpassword', $query['psw']);
            $this->assertEquals('3', $query['fmt']);
            $this->assertEquals('utf-8', $query['charset']);
            $this->assertEquals('71112223344', $query['phones']);
            $this->assertEquals('3', $query['cost']);
            $this->assertEquals('1', $query['err']);
            $this->assertEquals('1', $query['all']);
            $this->assertEquals('Hello world!', $query['mes']);
            $this->assertEquals('easy-sms.ru', $query['sender']);
            $this->assertEquals('12', $query['id']);

            $calledSend = true;

            return new Response(
                200,
                [],
                <<<JSON
{
    "id":"12",
    "cnt":"1",
    "cost":"3.1499999999999999",
    "balance":"unlimited",
    "phones":[
        {"phone":"71112223344", "cost":"3.1499999999999999", "status":"-1"}
    ]
}
JSON
            );
        });

        $client = new EasySmsHttpClient(
            $this->guzzleClient,
            $this->serializer,
            $baseOptions
        );
        $result = $client->sendSms($request);

        $this->assertTrue($calledSend);

        $this->assertSame('12', $result->id);
        $this->assertSame(1, $result->cnt);
        $this->assertSame(3.15, $result->cost);
        $this->assertSame('unlimited', $result->balance);

        $this->assertCount(1, $result->phones);
        $phone = reset($result->phones);
        $this->assertInstanceOf(ResponsePhone::class, $phone);
        $this->assertSame('71112223344', $phone->phone);
        $this->assertSame(3.15, $phone->cost);
        $this->assertSame(-1, $phone->status);
    }

    public function dataSendSmsHappyPathWithDifferentConfigurations(): array
    {
        return [
            'all options configured in method parameter' => [
                'request' => $this->createFullSmsRequest(),
                'baseOptions' => null,
            ],
            'login options in client constructor, request has only sms text' => [
                'request' => (new SendSmsRequest())
                    ->setPhone('71112223344')
                    ->setMessage('Hello world!')
                    ->setId('12'),
                'baseOptions' => $this->createBaseOptions(),
            ],
            'request can override everything from base constructor options' => [
                'request' => $this->createFullSmsRequest(),
                'baseOptions' => (new BaseOptions())
                    ->setLogin('wrong')
                    ->setPassword('wrong')
                    ->setFmt('-')
                    ->setCharset('latin1')
                    ->setReturnCost('-')
                    ->setErr('-')
                    ->setAll('-')
                    ->setSender('-')
                    ->setConnectId('111'),
            ],
        ];
    }

    public function testSendSmsWhenMultiSmsBody(): void
    {
        $request = $this->createFullSmsRequest();
        $request->setMessage('Тест1234567890123456789012345678901234567890123456789012345678901234567890');

        $this->mockHandler->append(new Response(
            200,
            [],
            <<<JSON
{
    "id":"12",
    "cnt":"2",
    "cost":"6.2999999999999998",
    "balance":"unlimited",
    "phones":[
        {"phone":"71112223344", "cost":"3.1499999999999999", "status":"-1"}
    ]
}
JSON
        ));

        $result = $this->client->sendSms($request);

        $this->assertSame(2, $result->cnt);
        $this->assertSame(6.3, $result->cost);

        $this->assertCount(1, $result->phones);
        $phone = reset($result->phones);
        $this->assertInstanceOf(ResponsePhone::class, $phone);
        $this->assertSame(3.15, $phone->cost);
    }

    public function testSendSmsWhenPhonesMissingInResponse(): void
    {
        $request = $this->createFullSmsRequest();

        $this->mockHandler->append(new Response(
            200,
            [],
            <<<JSON
{
    "id":"12",
    "cnt":"1",
    "cost":"3.1499999999999999",
    "balance":"unlimited",
    "phones":""
}
JSON
        ));

        $result = $this->client->sendSms($request);

        $this->assertSame([], $result->phones);
    }

    public function testSendSmsWhenHttpErrorShouldThrowException(): void
    {
        $request = $this->createFullSmsRequest();

        $this->mockHandler->append(new Response(500, [], ''));

        $this->expectException(TransportException::class);

        $this->client->sendSms($request);
    }

    /**
     * @dataProvider dataSendSmsWhenUnknownResponse
     */
    public function testSendSmsWhenUnknownResponseFormatShouldThrowException(
        ?string $responseText
    ): void {
        $request = $this->createFullSmsRequest();

        $this->mockHandler->append(new Response(200, [], $responseText));

        $this->expectException(BadResponseException::class);

        $this->client->sendSms($request);
    }

    public function dataSendSmsWhenUnknownResponse(): array
    {
        return [
            'empty string' => [
                'responseText' => '',
            ],
            'not a json' => [
                'responseText' => 'OK',
            ],
            'null' => [
                'responseText' => null,
            ],
        ];
    }
}
