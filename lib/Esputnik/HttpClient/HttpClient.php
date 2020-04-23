<?php

namespace Esputnik\HttpClient;

use Esputnik\Exception\ErrorException;
use Esputnik\Exception\RuntimeException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class HttpClient
{
    protected $options = [
        'api_version' => 'v1',
        'user_agent' => 'php-esputnik-api (http://)',
        'cache_dir' => null,
        'content_type' => 'application/json; charset=UTF-8',
    ];

    protected $headers = [];
    protected $lastRequest;
    protected $lastResponse;

    /**
     * @param array $options
     * @param ClientInterface $client
     */
    public function __construct(array $options = [], ClientInterface $client = null)
    {
        $this->clearHeaders();

        $this->options = array_merge($this->options, $options);
        $client = $client ?: new GuzzleClient([
            'base_uri' => sprintf('%s%s/', $this->options['base_url'], $this->options['api_version']),
            'auth' => [$this->options['login'], $this->options['password']],
            'debug' => false,
            'headers' => $this->headers,
        ]);

        $this->client = $client;
    }

    /**
     * {@inheritDoc}
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    /**
     * Clears used headers.
     */
    public function clearHeaders()
    {
        $this->headers = [
            'Accept' => 'application/json',
            'User-Agent' => sprintf('%s', $this->options['user_agent']),
            'Content-Type' => sprintf('%s', $this->options['content_type']),
        ];
    }


    /**
     * {@inheritDoc}
     */
    public function get($path, array $query = [], $parameters = [])
    {
        $this->options['query'] = $query;

        if (isset($parameters['maxrows'])) {
            $this->options['query']['maxrows'] = $parameters['maxrows'];
        }

        if (isset($parameters['startindex'])) {
            $this->options['query']['startindex'] = $parameters['startindex'];
        }

        return $this->request($path, null, 'GET');
    }

    /**
     * {@inheritDoc}
     */
    public function post($path, $body = null)
    {
        return $this->request($path, $body, 'POST');
    }

    /**
     * {@inheritDoc}
     */
    public function patch($path, $body = null)
    {
        return $this->request($path, $body, 'PATCH');
    }

    /**
     * {@inheritDoc}
     */
    public function delete($path, $body = null)
    {
        return $this->request($path, $body, 'DELETE');
    }

    /**
     * {@inheritDoc}
     */
    public function put($path, $body)
    {
        return $this->request($path, $body, 'PUT');
    }

    /**
     * {@inheritDoc}
     */
    public function request($path, $body = null, $httpMethod = 'GET', array $headers = [])
    {
        $request = $this->createRequest($httpMethod, $path, $body, $headers);

        try {
            $response = $this->client->send($request, $this->options);
        } catch (\LogicException $e) {
            throw new ErrorException($e->getMessage(), $e->getCode(), $e);
        } catch (\RuntimeException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        $this->lastRequest = $request;
        $this->lastResponse = $response;

        return $response;
    }

    /**
     * @return Request
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * @return Response
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    protected function createRequest($httpMethod, $path, $body = null, array $headers = [])
    {
        return new Request(
            $httpMethod,
            $path,
            $headers,
            $body
        );
    }
}
