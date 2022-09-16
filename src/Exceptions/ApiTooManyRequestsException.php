<?php

namespace NotificationChannels\GoUnisender\Exceptions;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class ApiTooManyRequestsException extends \RuntimeException {
  protected $response;

  public function __construct(RequestException $exception, ResponseInterface $response) {
    parent::__construct('Слишком много запросов, уменьшите их частоту.', $response->getStatusCode(), $exception);

    $this->response = $response;
  }

  public function getResponse(): ResponseInterface {
    return $this->response;
  }
}