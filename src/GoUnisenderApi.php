<?php

namespace NotificationChannels\GoUnisender;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Str;
use NotificationChannels\GoUnisender\Exceptions\ApiBadRequestException;
use NotificationChannels\GoUnisender\Exceptions\ApiForbiddenException;
use NotificationChannels\GoUnisender\Exceptions\ApiInternalServerErrorException;
use NotificationChannels\GoUnisender\Exceptions\ApiNotFoundException;
use NotificationChannels\GoUnisender\Exceptions\ApiRequestFailedException;
use NotificationChannels\GoUnisender\Exceptions\ApiRequestTooLargeException;
use NotificationChannels\GoUnisender\Exceptions\ApiTooManyRequestsException;
use NotificationChannels\GoUnisender\Exceptions\ApiUnathorizedException;

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

  public function sendEmail(GoUnisenderMessage $message) {
    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
      'X-API-KEY' => $this->token,
    ];
    $client = $this->getClient();

    $requestBody = [
      "message" => [
        "recipients" => [
          [
            "email" => $message->to,
            "substitutions" => $message->substitutions,
          ],
        ],
        "template_id" => $message->template,
        "body" => [
          "plaintext" => "Hello, {{name}}",
        ],
      ],
    ];

    try {
      $response = $client->requestAsync('POST', 'email/send.json', [
        'headers' => $headers,
        'json' => $requestBody,
      ]);
    } catch (\GuzzleHttp\Exception\BadResponseException $e) {
      switch ($e->getResponse()->getStatusCode()) {
        case '400':
          throw new ApiBadRequestException($e, $e->getResponse());
          break;
        case '401':
          throw new ApiUnathorizedException($e, $e->getResponse());
          break;
        case '403':
          throw new ApiForbiddenException($e, $e->getResponse());
          break;
        case '404':
          throw new ApiNotFoundException($e, $e->getResponse());
          break;
        case '413':
          throw new ApiRequestTooLargeException($e, $e->getResponse());
          break;
        case '429':
          throw new ApiTooManyRequestsException($e, $e->getResponse());
          break;
        case '500':
        case '501':
        case '502':
        case '503':
        case '504':
        case '505':
          throw new ApiInternalServerErrorException($e, $e->getResponse());
          break;
      };

      throw new ApiRequestFailedException($e, $e->getResponse());
    }
    }
}
