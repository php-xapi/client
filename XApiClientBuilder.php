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
use Xabbuh\XApi\Common\Serializer\ActorSerializer;
use Xabbuh\XApi\Common\Serializer\DocumentSerializer;
use Xabbuh\XApi\Common\Serializer\Serializer;
use Xabbuh\XApi\Common\Serializer\SerializerRegistry;
use Xabbuh\XApi\Common\Serializer\StatementResultSerializer;
use Xabbuh\XApi\Common\Serializer\StatementSerializer;

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
        $serializerRegistry = new SerializerRegistry();
        $serializerRegistry->setStatementSerializer(new StatementSerializer($serializer));
        $serializerRegistry->setStatementResultSerializer(new StatementResultSerializer($serializer));
        $serializerRegistry->setActorSerializer(new ActorSerializer($serializer));
        $serializerRegistry->setDocumentSerializer(new DocumentSerializer($serializer));

        $version = null === $this->version ? '1.0.1' : $this->version;
        $requestHandler = new Handler($httpClient, $version, $this->username, $this->password);
        $xApiClient = new XApiClient($requestHandler, $serializerRegistry, $this->version);

        return $xApiClient;
    }
}
