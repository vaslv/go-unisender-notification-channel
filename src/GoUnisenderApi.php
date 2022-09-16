<?php

namespace NotificationChannels\GoUnisender;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use NotificationChannels\GoUnisender\Exceptions\ApiBadRequestException;
use NotificationChannels\GoUnisender\Exceptions\ApiForbiddenException;
use NotificationChannels\GoUnisender\Exceptions\ApiInternalServerErrorException;
use NotificationChannels\GoUnisender\Exceptions\ApiNotFoundException;
use NotificationChannels\GoUnisender\Exceptions\ApiRequestFailedException;
use NotificationChannels\GoUnisender\Exceptions\ApiRequestTooLargeException;
use NotificationChannels\GoUnisender\Exceptions\ApiTooManyRequestsException;
use NotificationChannels\GoUnisender\Exceptions\ApiUnathorizedException;

class GoUnisenderApi {
  public const BASE_URI = 'https://go1.unisender.ru/ru/transactional/api/v1/';

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

  public function sendEmail(GoUnisenderMessage $message): void {
    $requestBody = [
      'message' => [
        'recipients' => [
          [
            'email' => $message->to,
            'substitutions' => $message->substitutions,
          ],
        ],
      ],
    ];

    if (!empty($message->templateId)) {
      $requestBody['message']['template_id'] = $message->templateId;
    }

    if (!empty($message->body)) {
      $requestBody['message']['body'] = $message->body;
    }

    if (!empty($message->subject)) {
      $requestBody['message']['subject'] = $message->subject;
    }

    if (!empty($message->globalSubstitutions)) {
      $requestBody['message']['global_substitutions'] = $message->globalSubstitutions;
    }

    if (!empty($message->globalLanguage)) {
      $requestBody['message']['global_language'] = $message->globalLanguage;
    }

    if (!is_null($message->skipUnsubscribe)) {
      $requestBody['message']['skip_unsubscribe'] = $message->skipUnsubscribe;
    }

    $this->request('email/send.json', $requestBody);
  }

  /**
   * @param string $uri URI
   * @param array $body Тело запроса
   * @param array $headers Дополнительные заголовки
   * @return void
   */
  private function request(string $uri, array $body, array $headers = []): void {
    $headers = array_merge($headers, [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
      'X-API-KEY' => $this->token,
    ]);

    $client = $this->getClient();

    try {
      $client->requestAsync('POST', $uri, [
        'headers' => $headers,
        'json' => $body,
      ]);
    } catch (BadResponseException $e) {
      switch ($e->getResponse()->getStatusCode()) {
        case '400':
          throw new ApiBadRequestException($e, $e->getResponse());
        case '401':
          throw new ApiUnathorizedException($e, $e->getResponse());
        case '403':
          throw new ApiForbiddenException($e, $e->getResponse());
        case '404':
          throw new ApiNotFoundException($e, $e->getResponse());
        case '413':
          throw new ApiRequestTooLargeException($e, $e->getResponse());
        case '429':
          throw new ApiTooManyRequestsException($e, $e->getResponse());
        case '500':
        case '501':
        case '502':
        case '503':
        case '504':
        case '505':
          throw new ApiInternalServerErrorException($e, $e->getResponse());
      }

      throw new ApiRequestFailedException($e, $e->getResponse());
    }
  }
}
