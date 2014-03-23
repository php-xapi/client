<?php

/*
 * This file is part of the XabbuhXApiClient package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\XApi\Client\Tests;

use Xabbuh\XApi\Client\XApiClient;
use Xabbuh\XApi\Common\Model\Statement;
use Xabbuh\XApi\Common\Model\StatementResult;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class XApiClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Guzzle\Http\ClientInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $httpClient;

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
        $this->httpClient = $this->createHttpClientMock();
        $this->serializer = $this->createSerializerMock();
        $this->client = new XApiClient($this->httpClient, $this->serializer, '1.0.1');
    }

    public function testStoreStatement()
    {
        $statementId = '12345678-1234-5678-1234-567812345678';
        $statement = $this->createStatement();
        $this->validateStoreApiCall(
            'post',
            'statements',
            200,
            '["'.$statementId.'"]',
            $statement
        );

        $this->assertEquals($statementId, $this->client->storeStatement($statement));
    }

    public function testStoreStatementWithId()
    {
        $statementId = '12345678-1234-5678-1234-567812345678';
        $statement = $this->createStatement();
        $statement->setId($statementId);
        $this->validateStoreApiCall(
            'put',
            'statements?statementId='.$statementId,
            204,
            '["'.$statementId.'"]',
            $statement
        );

        $this->assertEquals(null, $this->client->storeStatement($statement));
    }

    public function testStoreStatements()
    {
        $statementId1 = '12345678-1234-5678-1234-567812345678';
        $statementId2 = '12345678-1234-5678-1234-567812345679';
        $statement1 = $this->createStatement();
        $statement2 = $this->createStatement();
        $this->validateStoreApiCall(
            'post',
            'statements',
            '200',
            '["'.$statementId1.'","'.$statementId2.'"]',
            array($statement1, $statement2)
        );

        $this->assertEquals(
            array($statementId1, $statementId2),
            $this->client->storeStatements(array($statement1, $statement2))
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testStoreStatementsWithNonStatementObject()
    {
        $statement1 = $this->createStatement();
        $statement2 = $this->createStatement();

        $this->client->storeStatements(array($statement1, new \stdClass(), $statement2));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testStoreStatementsWithNonObject()
    {
        $statement1 = $this->createStatement();
        $statement2 = $this->createStatement();

        $this->client->storeStatements(array($statement1, 'foo', $statement2));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testStoreStatementsWithId()
    {
        $statement1 = $this->createStatement();
        $statement2 = $this->createStatement();
        $statement2->setId('12345678-1234-5678-1234-567812345679');

        $this->client->storeStatements(array($statement1, $statement2));
    }

    public function testGetStatement()
    {
        $statementId = '12345678-1234-5678-1234-567812345678';
        $statement = $this->createStatement();
        $this->validateRetrieveApiCall(
            'get',
            'statements?statementId='.$statementId,
            200,
            'Statement',
            $statement
        );

        $this->client->getStatement($statementId);
    }

    /**
     * @expectedException \Xabbuh\XApi\Common\Exception\NotFoundException
     */
    public function testGetStatementWithNotExistingStatement()
    {
        $statementId = '12345678-1234-5678-1234-567812345678';
        $this->validateRetrieveApiCall(
            'get',
            'statements?statementId='.$statementId,
            404,
            'Statement',
            'There is no statement associated with this id'
        );

        $this->client->getStatement($statementId);
    }

    public function testGetStatements()
    {
        $statementResult = $this->createStatementResult();
        $this->validateRetrieveApiCall(
            'get',
            'statements',
            200,
            'StatementResult',
            $statementResult
        );

        $this->assertEquals($statementResult, $this->client->getStatements());
    }

    private function createHttpClientMock()
    {
        return $this->getMock('\Guzzle\Http\ClientInterface');
    }

    private function createSerializerMock()
    {
        return $this->getMock('\JMS\Serializer\SerializerInterface');
    }

    private function createRequestMock($response = null)
    {
        $request = $this->getMock('\Guzzle\Http\Message\RequestInterface');

        if (null !== $response) {
            $request->expects($this->any())
                ->method('send')
                ->will($this->returnValue($response));
        }

        return $request;
    }

    private function createResponseMock($statusCode, $body)
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

    /**
     * @return \Xabbuh\XApi\Common\Model\StatementInterface
     */
    private function createStatement()
    {
        return new Statement();
    }

    /**
     * @return \Xabbuh\XApi\Common\Model\StatementResultInterface
     */
    private function createStatementResult()
    {
        return new StatementResult();
    }

    private function validateSerializer($data, $returnValue)
    {
        $this->serializer
            ->expects($this->once())
            ->method('serialize')
            ->with($data, 'json')
            ->will($this->returnValue($returnValue));
    }

    private function validateDeserializer($data, $type, $returnValue)
    {
        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with($data, 'Xabbuh\XApi\Common\Model\\'.$type, 'json')
            ->will($this->returnValue($returnValue));
    }

    private function validateRequest($method, $uri, $body = null, $response = null)
    {
        $request = $this->createRequestMock($response);

        if (null !== $body) {
            $this->httpClient
                ->expects($this->once())
                ->method($method)
                ->with($uri, null, $body)
                ->will($this->returnValue($request));
        } else {
            $this->httpClient
                ->expects($this->once())
                ->method($method)
                ->with($uri)
                ->will($this->returnValue($request));
        }
    }

    private function validateRetrieveApiCall($method, $uri, $statusCode, $type, $transformedResult)
    {
        $rawResponse = 'the-server-response';
        $response = $this->createResponseMock($statusCode, $rawResponse);
        $this->validateRequest($method, $uri, null, $response);

        if ($statusCode < 400) {
            $this->validateDeserializer($rawResponse, $type, $transformedResult);
        }
    }

    private function validateStoreApiCall($method, $uri, $statusCode, $rawResponse, $object)
    {
        $rawRequest = 'the-request-body';
        $response = $this->createResponseMock($statusCode, $rawResponse);
        $this->validateSerializer($object, $rawRequest);
        $this->validateRequest($method, $uri, $rawRequest, $response);
    }
}
