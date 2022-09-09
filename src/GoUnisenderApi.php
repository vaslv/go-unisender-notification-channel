<?php

namespace NotificationChannels\GoUnisender;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Str;
use NotificationChannels\GoUnisender\Exceptions\ApiRequestFailedException;

class GoUnisenderApi {
    const BASE_URI = 'https://go1.unisender.ru/ru/transactional/api/v1/';

    protected $token;
    protected $client;

    public function __construct(string $token = NULL) {
        $this->token = $token;
        $this->client = NULL;
    }

    public function setToken(string $token): void {
        $this->token = $token;
        $this->client = NULL;
    }

    public function getClient(): Client {
        if (!is_null($this->client)) {
            return $this->client;
        }

        return $this->client = new Client([
            'base_uri' => static::BASE_URI,
        ]);
    }

    public function performRequest(string $method, string $url, array $options = []): array {
        $method = Str::upper($method);
        $options = $this->withDefaultOptions($method, $options);

        try {
            $response = $this->getClient()->request($method, $url, $options);
        } catch (RequestException|GuzzleException $e) {
            throw new ApiRequestFailedException($e, $e->getResponse());
        }

        return (array)json_decode((string)$response->getBody(), TRUE);
    }

    protected function withDefaultOptions(string $method, array $options = []): array {
        $parametersBag = '';

        switch ($method) {
            case 'GET':
                $parametersBag = 'query';
                break;
            case 'POST':
                $parametersBag = 'form_params';
                break;
        }

        if (!isset($options[$parametersBag])) {
            $options[$parametersBag] = [];
        }

        $options[$parametersBag]['format'] = 'json';
        $options[$parametersBag]['api_key'] = $this->token;

        return $options;
    }

    public function sendSms(GoUnisenderMessage $message): array {
        return $this->performRequest('POST', 'sendSms', [
            'form_params' => [
                'phone' => $message->to,
                'sender' => $message->from,
                'text' => $message->content,
            ],
        ]);
    }
}
