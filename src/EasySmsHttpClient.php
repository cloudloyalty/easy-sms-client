<?php

declare(strict_types=1);

namespace CloudLoyalty\EasySmsClient;

use CloudLoyalty\EasySmsClient\Exception\BadResponseException;
use CloudLoyalty\EasySmsClient\Exception\BaseEasySmsException;
use CloudLoyalty\EasySmsClient\Exception\TransportException;
use CloudLoyalty\EasySmsClient\Model\BaseOptions;
use CloudLoyalty\EasySmsClient\Model\SendSmsRequest;
use CloudLoyalty\EasySmsClient\Model\SendSmsResponse;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\SerializerInterface;

class EasySmsHttpClient
{
    /** @var ClientInterface */
    private $client;

    /** @var SerializerInterface */
    private $serializer;

    /** @var BaseOptions|null */
    private $baseOptions;

    public function __construct(
        ClientInterface $client,
        SerializerInterface $serializer,
        BaseOptions $baseOptions = null
    ) {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->baseOptions = $baseOptions;
    }

    /**
     * @param SendSmsRequest $request
     * @return SendSmsResponse
     * @throws BaseEasySmsException
     */
    public function sendSms(SendSmsRequest $request): SendSmsResponse
    {
        try {
            $connectId = $this->baseOptions ? $this->baseOptions->getConnectId() : null;
            if ($request->getConnectId() !== null) {
                $connectId = $request->getConnectId();
            }

            $queryParams = array_merge(
                $this->baseOptions ? $this->baseOptions->asHttpQueryParams() : [],
                $request->asHttpQueryParams()
            );

            $httpResponse = $this->client->request(
                'GET',
                '/api/v1/smsc/' . $connectId . '/send_sms?' . http_build_query($queryParams),
                [
                    'http_errors' => true,
                ]
            );

            $responseBody = (string)$httpResponse->getBody();

            // Work around the cases when phone in reply is not an array.
            // ..."balance":"unlimited","phones":""}
            $struct = json_decode($responseBody, true);
            if ($struct && isset($struct['phones']) && !is_array($struct['phones'])) {
                $struct['phones'] = [];
                $responseBody = (string)json_encode($struct);
            }

            $smsResponse = $this->serializer->deserialize(
                $responseBody,
                SendSmsResponse::class,
                'json'
            );
            assert($smsResponse instanceof SendSmsResponse);
        } catch (TransferException $e) {
            throw new TransportException($e->getMessage(), 0, $e);
        } catch (RuntimeException $e) {
            throw new BadResponseException($e->getMessage(), 0, $e);
        }

        return $smsResponse;
    }
}
