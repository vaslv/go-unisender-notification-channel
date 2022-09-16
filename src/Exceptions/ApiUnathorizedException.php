<?php

namespace NotificationChannels\GoUnisender\Exceptions;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class ApiUnathorizedException extends \RuntimeException {
  protected $response;

  public function __construct(RequestException $exception, ResponseInterface $response) {
    parent::__construct('Неверный API-ключ.', $response->getStatusCode(), $exception);

    $this->response = $response;
  }

  public function getResponse(): ResponseInterface {
    return $this->response;
  }
}