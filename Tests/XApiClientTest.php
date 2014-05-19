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
     * @var \JMS\Serializer\SerializerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $serializer;

    /**
     * @var XApiClient
     */
    private $client;

    protected function setUp()
    {
        $this->requestHandler = $this->createRequestHandlerMock();
        $this->serializer = $this->createSerializerMock();
        $this->client = new XApiClient($this->requestHandler, $this->serializer, '1.0.1');
    }

    public function testGetRequestHandler()
    {
        $this->assertSame($this->requestHandler, $this->client->getRequestHandler());
    }

    public function testGetSerializer()
    {
        $this->assertSame($this->serializer, $this->client->getSerializer());
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
     * @return \JMS\Serializer\SerializerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createSerializerMock()
    {
        return $this->getMock('\JMS\Serializer\SerializerInterface');
    }
}
