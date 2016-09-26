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

use Xabbuh\XApi\Client\Request\HandlerInterface;
use Xabbuh\XApi\Client\XApiClient;
use Xabbuh\XApi\Serializer\SerializerRegistryInterface;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class XApiClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HandlerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestHandler;

    /**
     * @var SerializerRegistryInterface|\PHPUnit_Framework_MockObject_MockObject
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
     * @return HandlerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createRequestHandlerMock()
    {
        return $this->getMock('\Xabbuh\XApi\Client\Request\HandlerInterface');
    }

    /**
     * @return SerializerRegistryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createSerializerRegistryMock()
    {
        $serializerRegistry = $this->getMock('\Xabbuh\XApi\Serializer\SerializerRegistryInterface');

        $statementSerializer = $this->getMock('\Xabbuh\XApi\Serializer\StatementSerializerInterface');
        $serializerRegistry
            ->expects($this->any())
            ->method('getStatementSerializer')
            ->willReturn($statementSerializer);

        $statementResultSerializer = $this->getMock('\Xabbuh\XApi\Serializer\StatementResultSerializerInterface');
        $serializerRegistry
            ->expects($this->any())
            ->method('getStatementResultSerializer')
            ->willReturn($statementResultSerializer);

        $actorSerializer = $this->getMock('\Xabbuh\XApi\Serializer\ActorSerializerInterface');
        $serializerRegistry
            ->expects($this->any())
            ->method('getActorSerializer')
            ->willReturn($actorSerializer);

        $documentDataSerializer = $this->getMock('\Xabbuh\XApi\Serializer\DocumentDataSerializerInterface');
        $serializerRegistry
            ->expects($this->any())
            ->method('getDocumentDataSerializer')
            ->willReturn($documentDataSerializer);

        return $serializerRegistry;
    }
}
