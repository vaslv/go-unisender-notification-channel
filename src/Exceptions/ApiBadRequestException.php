<?php

namespace NotificationChannels\GoUnisender\Exceptions;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class ApiBadRequestException extends \RuntimeException {
  protected $response;

  public function __construct(RequestException $exception, ResponseInterface $response) {
    parent::__construct('Запрос ошибочен. Проверьте формат запроса и состав параметров', $response->getStatusCode(), $exception);

    $this->response = $response;
  }

  public function getResponse(): ResponseInterface {
    return $this->response;
  }
}