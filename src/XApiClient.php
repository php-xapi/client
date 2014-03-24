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
use Xabbuh\XApi\Common\Exception\AccessDeniedException;
use Xabbuh\XApi\Common\Exception\ConflictException;
use Xabbuh\XApi\Common\Exception\NotFoundException;
use Xabbuh\XApi\Common\Exception\XApiException;
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

    private $version;

    private $username;

    private $password;

    /**
     * @param ClientInterface     $httpClient The HTTP client
     * @param SerializerInterface $serializer The serializer
     * @param string              $version    The xAPI version
     */
    public function __construct(ClientInterface $httpClient, SerializerInterface $serializer, $version)
    {
        $this->httpClient = $httpClient;
        $this->serializer = $serializer;
        $this->version = $version;
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
     * Returns the xAPI version.
     *
     * @return string The xAPI version
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Returns the HTTP auth username.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Returns the HTTP auth password.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritDoc}
     */
    public function storeStatement(StatementInterface $statement)
    {
        if (null !== $statement->getId()) {
            $request = $this->createRequest(
                'put',
                'statements',
                array('statementId' => $statement->getId()),
                $this->serializer->serialize($statement, 'json')
            );
            $this->performRequest($request, array(204));
        } else {
            $request = $this->createRequest(
                'post',
                'statements',
                array(),
                $this->serializer->serialize($statement, 'json')
            );
            $response = $this->performRequest($request, array(200));
            $contents = json_decode($response->getBody(true));
            $statement->setId($contents[0]);
        }

        return $statement;
    }

    /**
     * {@inheritDoc}
     */
    public function storeStatements(array $statements)
    {
        // check that only Statements without ids will be sent to the LRS
        foreach ($statements as $statement) {
            if (!is_object($statement)) {
                throw new \InvalidArgumentException(
                    'API can not handle '.gettype($statement).' values'
                );
            }

            if (!$statement instanceof StatementInterface) {
                throw new \InvalidArgumentException(
                    'API can not  handle objects of type '.get_class($statement)
                );
            }

            if (null !== $statement->getId()) {
                throw new \InvalidArgumentException(
                    'API can not handle Statements with ids when storing multiple Statements'
                );
            }
        }

        $request = $this->createRequest(
            'post',
            'statements',
            array(),
            $this->serializer->serialize($statements, 'json')
        );
        $response = $this->performRequest($request, array(200));

        foreach (json_decode($response->getBody(true)) as $key => $statementId) {
            $statements[$key]->setId($statementId);
        }

        return $statements;
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
        $response = $this->performRequest($request, array(200));

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
        $response = $this->performRequest($request, array(200));

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

        $request->addHeader('X-Experience-API-Version', $this->version);
        $request->addHeader('Content-Type', 'application/json');
        $request->setAuth($this->username, $this->password);

        return $request;
    }

    /**
     * Performs the given HTTP request.
     *
     * @param RequestInterface $request          The HTTP request to perform
     * @param int[]            $validStatusCodes A list of HTTP status codes
     *                                           the calling method is able to
     *                                           handle
     *
     * @return \Guzzle\Http\Message\Response The remote server's response
     *
     * @throws XApiException when the request fails
     */
    private function performRequest(RequestInterface $request, array $validStatusCodes)
    {
        try {
            $response = $request->send();
        } catch (ClientErrorResponseException $e) {
            $response = $e->getResponse();
        }

        // catch some common errors
        if (in_array($response->getStatusCode(), array(401, 403))) {
            throw new AccessDeniedException(
                $response->getBody(true),
                $response->getStatusCode()
            );
        } elseif (404 === $response->getStatusCode()) {
            throw new NotFoundException($response->getBody(true));
        } elseif (409 === $response->getStatusCode()) {
            throw new ConflictException($response->getBody(true));
        }

        if (!in_array($response->getStatusCode(), $validStatusCodes)) {
            throw new XApiException($response->getBody(true), $response->getStatusCode());
        }

        return $response;
    }
}
