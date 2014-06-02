<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\XApi\Client\Tests;

use Xabbuh\XApi\Client\XApiClient;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class XApiClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Xabbuh\XApi\Client\Request\HandlerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestHandler;

    /**
     * @var \Xabbuh\XApi\Common\Serializer\SerializerRegistryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $serializerRegistry;

    /**
     * @var XApiClient
     */
    private $client;

    protected function setUp()
    {
        $this->requestHandler = $this->createRequestHandlerMock();
        $this->serializerRegistry = $this->createSerializerRegistryMock();
        $this->client = new XApiClient($this->requestHandler, $this->serializerRegistry, '1.0.1');
    }

    public function testGetRequestHandler()
    {
        $this->assertSame($this->requestHandler, $this->client->getRequestHandler());
    }

    public function testGetSerializerRegistry()
    {
        $this->assertSame($this->serializerRegistry, $this->client->getSerializerRegistry());
    }

    public function testGetStatementsApi()
    {
        $this->assertInstanceOf(
            'Xabbuh\XApi\Client\Api\StatementsApiClientInterface',
            $this->client->getStatementsApiClient()
        );
    }

    public function testGetStateApi()
    {
        $this->assertInstanceOf(
            'Xabbuh\XApi\Client\Api\StateApiClientInterface',
            $this->client->getStateApiClient()
        );
    }

    public function testGetActivityProfileApi()
    {
        $this->assertInstanceOf(
            'Xabbuh\XApi\Client\Api\ActivityProfileApiClientInterface',
            $this->client->getActivityProfileApiClient()
        );
    }

    public function testGetAgentProfileApi()
    {
        $this->assertInstanceOf(
            'Xabbuh\XApi\Client\Api\AgentProfileApiClientInterface',
            $this->client->getAgentProfileApiClient()
        );
    }

    /**
     * @return \Xabbuh\XApi\Client\Request\HandlerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createRequestHandlerMock()
    {
        return $this->getMock('\Xabbuh\XApi\Client\Request\HandlerInterface');
    }

    /**
     * @return \Xabbuh\XApi\Common\Serializer\SerializerRegistryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createSerializerRegistryMock()
    {
        return $this->getMock('\Xabbuh\XApi\Common\Serializer\SerializerRegistryInterface');
    }
}
