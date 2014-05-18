<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\XApi\Client\Api;

use Guzzle\Http\ClientInterface;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Message\RequestInterface;
use Xabbuh\XApi\Common\Exception\AccessDeniedException;
use Xabbuh\XApi\Common\Exception\ConflictException;
use Xabbuh\XApi\Common\Exception\NotFoundException;
use Xabbuh\XApi\Common\Exception\XApiException;

/**
 * Prepare and execute xAPI HTTP requests.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class RequestExecutor implements RequestExecutorInterface
{
    private $httpClient;

    private $version;

    private $username;

    private $password;

    /**
     * @param ClientInterface     $httpClient The HTTP client
     * @param string              $version    The xAPI version
     * @param string              $username   The optional HTTP auth username
     * @param string              $password   The optional HTTP auth password
     */
    public function __construct(ClientInterface $httpClient, $version, $username = null, $password = null)
    {
        $this->httpClient = $httpClient;
        $this->version = $version;
        $this->username = $username;
        $this->password = $password;
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
     * {@inheritDoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritDoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritDoc}
     */
    public function createRequest($method, $uri, array $urlParameters = array(), $body = null)
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
            case 'delete':
                $request = $this->httpClient->delete($uri);
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
     * {@inheritDoc}
     */
    public function executeRequest(RequestInterface $request, array $validStatusCodes)
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
