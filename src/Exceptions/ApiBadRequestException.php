<?php

namespace NotificationChannels\GoUnisender\Exceptions;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class ApiBadRequestException extends GoUnisenderException {
  protected $response;
  protected $body = [];

  public function __construct(RequestException $exception, ResponseInterface $response, array $body = []) {
    parent::__construct('Запрос ошибочен. Проверьте формат запроса и состав параметров', $response->getStatusCode(), $exception);

    $this->response = $response;
    $this->body = $body;
  }

  public function getResponse(): ResponseInterface {
    return $this->response;
  }

  public function getBody(): array {
    return $this->body;
  }
}