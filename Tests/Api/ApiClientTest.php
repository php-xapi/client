<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\XApi\Client\Tests\Api;

use Xabbuh\XApi\Common\Exception\NotFoundException;
use Xabbuh\XApi\Common\Serializer\ActorSerializer;
use Xabbuh\XApi\Common\Serializer\DocumentSerializer;
use Xabbuh\XApi\Common\Serializer\SerializerRegistry;
use Xabbuh\XApi\Common\Serializer\StatementResultSerializer;
use Xabbuh\XApi\Common\Serializer\StatementSerializer;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
abstract class ApiClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Xabbuh\XApi\Client\Request\HandlerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestHandler;

    /**
     * @var \JMS\Serializer\SerializerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $serializer;

    /**
     * @var SerializerRegistry
     */
    protected $serializerRegistry;

    protected function setUp()
    {
        $this->requestHandler = $this->createRequestHandlerMock();
        $this->serializer = $this->createSerializerMock();
        $this->serializerRegistry = $this->createSerializerRegistry();
    }

    protected function createRequestHandlerMock()
    {
        return $this->getMock('\Xabbuh\XApi\Client\Request\HandlerInterface');
    }

    protected function createSerializerRegistry()
    {
        $registry = new SerializerRegistry();
        $registry->setStatementSerializer(new StatementSerializer($this->serializer));
        $registry->setStatementResultSerializer(new StatementResultSerializer($this->serializer));
        $registry->setActorSerializer(new ActorSerializer($this->serializer));
        $registry->setDocumentSerializer(new DocumentSerializer($this->serializer));

        return $registry;
    }

    protected function createSerializerMock()
    {
        return $this->getMock('\JMS\Serializer\SerializerInterface');
    }

    protected function validateDeserializer($data, $type, $returnValue)
    {
        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with($data, 'Xabbuh\XApi\Model\\'.$type, 'json')
            ->will($this->returnValue($returnValue));
    }

    protected function validateSerializer(array $serializerMap)
    {
        $this
            ->serializer
            ->expects($this->any())
            ->method('serialize')
            ->will($this->returnCallback(function ($data) use ($serializerMap) {
                foreach ($serializerMap as $entry) {
                    if ($data == $entry['data']) {
                        return $entry['result'];
                    }
                }

                return '';
            }));
    }

    protected function createRequestMock($response = null)
    {
        $request = $this->getMock('\Guzzle\Http\Message\RequestInterface');

        if (null !== $response) {
            $request->expects($this->any())
                ->method('send')
                ->will($this->returnValue($response));
        }

        return $request;
    }

    protected function createResponseMock($statusCode, $body)
    {
        $response = $this->getMock(
            '\Guzzle\Http\Message\Response',
            array(),
            array($statusCode)
        );
        $response->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue($statusCode));
        $response->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($body));

        return $response;
    }

    protected function validateRequest($method, $uri, array $urlParameters, $body = null, $response = null)
    {
        $request = $this->createRequestMock($response);
        $this
            ->requestHandler
            ->expects($this->once())
            ->method('createRequest')
            ->with($method, $uri, $urlParameters, $body)
            ->will($this->returnValue($request));

        return $request;
    }

    protected function validateRetrieveApiCall($method, $uri, array $urlParameters, $statusCode, $type, $transformedResult, array $serializerMap = array())
    {
        $rawResponse = 'the-server-response';
        $response = $this->createResponseMock($statusCode, $rawResponse);
        $request = $this->validateRequest($method, $uri, $urlParameters, null, $response);

        if (404 === $statusCode) {
            $this
                ->requestHandler
                ->expects($this->once())
                ->method('executeRequest')
                ->with($request)
                ->will($this->throwException(new NotFoundException('Not found')));
        } else {
            $this
                ->requestHandler
                ->expects($this->once())
                ->method('executeRequest')
                ->with($request)
                ->will($this->returnValue($response));
        }

        $this->validateSerializer($serializerMap);

        if ($statusCode < 400) {
            $this->validateDeserializer($rawResponse, $type, $transformedResult);
        }
    }

    protected function validateStoreApiCall($method, $uri, array $urlParameters, $statusCode, $rawResponse, $object, array $serializerMap = array())
    {
        $rawRequest = 'the-request-body';
        $response = $this->createResponseMock($statusCode, $rawResponse);
        $request = $this->validateRequest($method, $uri, $urlParameters, $rawRequest, $response);
        $this
            ->requestHandler
            ->expects($this->once())
            ->method('executeRequest')
            ->with($request, array($statusCode))
            ->will($this->returnValue($response));
        $serializerMap[] = array('data' => $object, 'result' => $rawRequest);
        $this->validateSerializer($serializerMap);
    }

    protected function validateDeleteDocumentCall($uri, array $urlParameters, array $serializerMap = array())
    {
        $response = $this->createResponseMock(204, '');
        $this->validateRequest('delete', $uri, $urlParameters, '', $response);
        $this->validateSerializer($serializerMap);
    }
}
