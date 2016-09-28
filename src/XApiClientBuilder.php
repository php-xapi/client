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
use Xabbuh\XApi\Serializer\SerializerFactoryInterface;
use Xabbuh\XApi\Serializer\SerializerRegistry;
use Xabbuh\XApi\Serializer\Symfony\SerializerFactory;

/**
 * xAPI client builder.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
final class XApiClientBuilder implements XApiClientBuilderInterface
{
    private $serializerFactory;
    private $baseUrl;
    private $version;
    private $username;
    private $password;
    private $oAuthCredentials;

    public function __construct(SerializerFactoryInterface $serializerFactory = null)
    {
        $this->serializerFactory = $serializerFactory ?: new SerializerFactory();
    }

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
        if (null === $this->baseUrl) {
            throw new \LogicException('Base URI value was not configured.');
        }

        $httpClient = new Client($this->baseUrl);

        if (is_array($this->oAuthCredentials)) {
            $httpClient->addSubscriber(new OauthPlugin($this->oAuthCredentials));
        }

        $serializerRegistry = new SerializerRegistry();
        $serializerRegistry->setStatementSerializer($this->serializerFactory->createStatementSerializer());
        $serializerRegistry->setStatementResultSerializer($this->serializerFactory->createStatementResultSerializer());
        $serializerRegistry->setActorSerializer($this->serializerFactory->createActorSerializer());
        $serializerRegistry->setDocumentDataSerializer($this->serializerFactory->createDocumentDataSerializer());

        $version = null === $this->version ? '1.0.1' : $this->version;
        $requestHandler = new Handler($httpClient, $version, $this->username, $this->password);
        $xApiClient = new XApiClient($requestHandler, $serializerRegistry, $this->version);

        return $xApiClient;
    }
}
