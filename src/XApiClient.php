<?php

/*
 * This file is part of the XabbuhXApiClient package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\XApi\Client;

use Guzzle\Http\ClientInterface;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Message\RequestInterface;
use JMS\Serializer\SerializerInterface;
use Xabbuh\XApi\Common\Model\StatementInterface;

/**
 * An Experience API client.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class XApiClient implements XApiClientInterface
{
    private $httpClient;

    private $serializer;

    private $username;

    private $password;

    /**
     * @param ClientInterface     $httpClient The HTTP client
     * @param SerializerInterface $serializer The serializer
     */
    public function __construct(ClientInterface $httpClient, SerializerInterface $serializer)
    {
        $this->httpClient = $httpClient;
        $this->serializer = $serializer;
    }

    /**
     * Returns the HTTP client used to perform the API requests.
     *
     * @return ClientInterface The HTTP client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Returns the serializer.
     *
     * @return SerializerInterface The serializer
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * Sets HTTP auth credentials.
     *
     * @param string $username The username
     * @param string $password The password
     */
    public function setAuth($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * {@inheritDoc}
     */
    public function storeStatement(StatementInterface $statement)
    {
        $request = $this->createRequest(
            null !== $statement->getId() ? 'put' : 'post',
            'statements',
            array(),
            $this->serializer->serialize($statement, 'json')
        );
        $response = $this->performRequest($request);
        $contents = json_decode($response->getBody(true));

        return $contents[0];
    }

    /**
     * {@inheritDoc}
     */
    public function getStatement($statementId)
    {
        $request = $this->createRequest(
            'get',
            'statements',
            array('statementId' => $statementId)
        );
        $response = $this->performRequest($request);

        return $this->serializer->deserialize(
            $response->getBody(true),
            'Xabbuh\XApi\Common\Model\Statement',
            'json'
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getStatements()
    {
        $request = $this->createRequest('get', 'statements');
        $response = $this->performRequest($request);

        return $this->serializer->deserialize(
            $response->getBody(true),
            'Xabbuh\XApi\Common\Model\StatementResult',
            'json'
        );
    }

    /**
     * @param string $method        The HTTP method
     * @param string $uri           The URI to send the request to
     * @param array  $urlParameters Optional url parameters
     * @param string $body          An optional request body
     *
     * @return RequestInterface The request
     *
     * @throws \InvalidArgumentException when no valid HTTP method is given
     */
    private function createRequest($method, $uri, array $urlParameters = array(), $body = null)
    {
        if (count($urlParameters) > 0) {
            $uri .= '?'.http_build_query($urlParameters);
        }

        switch ($method) {
            case 'get':
                $request = $this->httpClient->get($uri);
                break;
            case 'post':
                $request = $this->httpClient->post($uri, null, $body);
                break;
            case 'put':
                $request = $this->httpClient->put($uri, null, $body);
                break;
            default:
                throw new \InvalidArgumentException(
                    $method.' is no valid HTTP method'
                );
        }

        $request->addHeader('X-Experience-API-Version', '1.0.1');
        $request->addHeader('Content-Type', 'application/json');
        $request->setAuth($this->username, $this->password);

        return $request;
    }

    /**
     * Performs the given HTTP request.
     *
     * @param RequestInterface $request The HTTP request to perform
     *
     * @return \Guzzle\Http\Message\Response The remote server's response
     */
    private function performRequest(RequestInterface $request)
    {
        try {
            $response = $request->send();
        } catch (ClientErrorResponseException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }
}
