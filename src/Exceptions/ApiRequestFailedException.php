<?php

namespace NotificationChannels\GoUnisender\Exceptions;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class ApiRequestFailedException extends \RuntimeException {
    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * ApiRequestFailedException constructor.
     *
     * @param RequestException $exception
     * @param ResponseInterface $response
     */
    public function __construct(RequestException $exception, ResponseInterface $response) {
        parent::__construct('API Request Failed', $response->getStatusCode(), $exception);

        $this->response = $response;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface {
        return $this->response;
    }
}
