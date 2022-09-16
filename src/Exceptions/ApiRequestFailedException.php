<?php

namespace NotificationChannels\GoUnisender\Exceptions;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class ApiRequestFailedException extends GoUnisenderException {
    protected $response;

    public function __construct(RequestException $exception, ResponseInterface $response) {
        parent::__construct('API Request Failed', $response->getStatusCode(), $exception);

        $this->response = $response;
    }

    public function getResponse(): ResponseInterface {
        return $this->response;
    }
}
