<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\XApi\Client;

use Guzzle\Http\Client;
use Guzzle\Plugin\Oauth\OauthPlugin;
use Xabbuh\XApi\Client\Request\Handler;
use Xabbuh\XApi\Common\Serializer\Serializer;

/**
 * xAPI client builder.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class XApiClientBuilder implements XApiClientBuilderInterface
{
    private $baseUrl;

    private $version;

    private $username;

    private $password;

    private $oAuthCredentials;

    /**
     * {@inheritDoc}
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setAuth($username, $password)
    {
        $this->username = $username;
        $this->password = $password;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setOAuthCredentials($consumerKey, $consumerSecret, $token, $tokenSecret)
    {
        $this->oAuthCredentials = array(
            'consumer_key' => $consumerKey,
            'consumer_secret' => $consumerSecret,
            'token' => $token,
            'token_secret' => $tokenSecret,
        );

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function build()
    {
        $httpClient = new Client($this->baseUrl);

        if (is_array($this->oAuthCredentials)) {
            $httpClient->addSubscriber(new OauthPlugin($this->oAuthCredentials));
        }

        $serializer = Serializer::createSerializer();
        $version = null === $this->version ? '1.0.1' : $this->version;
        $requestHandler = new Handler($httpClient, $version, $this->username, $this->password);
        $xApiClient = new XApiClient($requestHandler, $serializer, $this->version);

        return $xApiClient;
    }
}
