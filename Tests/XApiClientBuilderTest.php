<?php

/*
 * This file is part of the XabbuhXApiClient package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Xabbuh\XApi\Client\XApiClientBuilder;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class XApiClientBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var XApiClientBuilder
     */
    private $builder;

    protected function setUp()
    {
        $this->builder = new XApiClientBuilder();
    }

    public function testSetBaseUrl()
    {
        $this->builder->setBaseUrl('http://www.example.com');
        $xApiClient = $this->builder->build();
        $httpClient = $xApiClient->getHttpClient();

        $this->assertEquals('http://www.example.com', $httpClient->getBaseUrl());
    }

    public function testSetVersion()
    {
        $this->builder->setVersion('1.0.0');
        $xApiClient = $this->builder->build();

        $this->assertEquals('1.0.0', $xApiClient->getVersion());
    }

    public function testSetAuth()
    {
        $this->builder->setAuth('foo', 'bar');
        $xApiClient = $this->builder->build();

        $this->assertEquals('foo', $xApiClient->getUsername());
        $this->assertEquals('bar', $xApiClient->getPassword());
    }

    public function testSetOAuth()
    {
        /** @var \Xabbuh\XApi\Client\XApiClient $xApiClient */
        $xApiClient = $this->builder
            ->setOAuthCredentials(
                'consumer-key',
                'consumer-secret',
                'token',
                'token-secret'
            )
            ->build();
        $httpClient = $xApiClient->getHttpClient();
        $listeners = $httpClient->getEventDispatcher()
            ->getListeners('request.before_send');

        foreach ($listeners as $index => $listener) {
            if (!$listener[0] instanceof \Guzzle\Plugin\Oauth\OauthPlugin) {
                unset($listeners[$index]);
            }
        }

        $this->assertCount(1, $listeners);
    }
}
