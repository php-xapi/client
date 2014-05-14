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

use Xabbuh\XApi\Client\StatementsFilter;
use Xabbuh\XApi\Client\XApiClient;
use Xabbuh\XApi\Common\Model\Activity;
use Xabbuh\XApi\Common\Model\ActivityProfile;
use Xabbuh\XApi\Common\Model\ActivityProfileDocument;
use Xabbuh\XApi\Common\Model\Agent;
use Xabbuh\XApi\Common\Model\State;
use Xabbuh\XApi\Common\Model\StateDocument;
use Xabbuh\XApi\Common\Model\Statement;
use Xabbuh\XApi\Common\Model\StatementReference;
use Xabbuh\XApi\Common\Model\StatementResult;
use Xabbuh\XApi\Common\Model\Verb;

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
            $this->createStatement()
        );
        $returnedStatement = $this->client->storeStatement($statement);
        $expectedStatement = $this->createStatement();
        $expectedStatement->setId($statementId);

        $this->assertEquals($expectedStatement, $returnedStatement);
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

        $this->assertEquals($statement, $this->client->storeStatement($statement));
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
            array($this->createStatement(), $this->createStatement())
        );
        $statements = $this->client->storeStatements(array($statement1, $statement2));
        $expectedStatement1 = $this->createStatement();
        $expectedStatement1->setId($statementId1);
        $expectedStatement2 = $this->createStatement();
        $expectedStatement2->setId($statementId2);
        $expectedStatements = array($expectedStatement1, $expectedStatement2);

        $this->assertNotContains($statements[0], array($statement1, $statement2));
        $this->assertNotContains($statements[1], array($statement1, $statement2));
        $this->assertEquals($expectedStatements, $statements);
        $this->assertEquals($statementId1, $statements[0]->getId());
        $this->assertEquals($statementId2, $statements[1]->getId());
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

    public function testVoidStatement()
    {
        $voidedStatementId = '12345678-1234-5678-1234-567812345679';
        $voidingStatementId = '12345678-1234-5678-1234-567812345678';
        $agent = new Agent();
        $agent->setMbox('mailto:john.doe@example.com');
        $statementReference = new StatementReference();
        $statementReference->setStatementId($voidedStatementId);
        $voidingStatement = $this->createStatement();
        $voidingStatement->setActor($agent);
        $voidingStatement->setVerb(Verb::createVoidVerb());
        $voidingStatement->setObject($statementReference);
        $voidedStatement = $this->createStatement();
        $voidedStatement->setId($voidedStatementId);
        $this->validateStoreApiCall(
            'post',
            'statements',
            200,
            '["'.$voidingStatementId.'"]',
            $voidingStatement
        );
        $returnedVoidingStatement = $this->client->voidStatement($voidedStatement, $agent);
        $expectedVoidingStatement = $this->createStatement();
        $expectedVoidingStatement->setId($voidingStatementId);
        $expectedVoidingStatement->setActor($agent);
        $expectedVoidingStatement->setVerb(Verb::createVoidVerb());
        $expectedVoidingStatement->setObject($statementReference);

        $this->assertEquals($expectedVoidingStatement, $returnedVoidingStatement);
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

    public function testGetVoidedStatement()
    {
        $statementId = '12345678-1234-5678-1234-567812345678';
        $statement = $this->createStatement();
        $this->validateRetrieveApiCall(
            'get',
            'statements?voidedStatementId='.$statementId,
            200,
            'Statement',
            $statement
        );

        $this->client->getVoidedStatement($statementId);
    }

    /**
     * @expectedException \Xabbuh\XApi\Common\Exception\NotFoundException
     */
    public function testGetVoidedStatementWithNotExistingStatement()
    {
        $statementId = '12345678-1234-5678-1234-567812345678';
        $this->validateRetrieveApiCall(
            'get',
            'statements?voidedStatementId='.$statementId,
            404,
            'Statement',
            'There is no statement associated with this id'
        );

        $this->client->getVoidedStatement($statementId);
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

    public function testGetStatementsWithStatementsFilter()
    {
        $filter = new StatementsFilter();
        $filter->limit(10)->ascending();
        $statementResult = $this->createStatementResult();
        $this->validateRetrieveApiCall(
            'get',
            'statements?limit=10&ascending=True',
            200,
            'StatementResult',
            $statementResult
        );

        $this->assertEquals($statementResult, $this->client->getStatements($filter));
    }

    public function testGetStatementsWithAgentInStatementsFilter()
    {
        // {"mbox":"mailto:alice@example.com","objectType":"Agent"}
        $filter = new StatementsFilter();
        $agent = new Agent();
        $agent->setMbox('mailto:alice@example.com');
        $filter->byActor($agent);
        $statementResult = $this->createStatementResult();
        $agentJson = '{"mbox":"mailto:alice@example.com","objectType":"Agent"}';
        $this->serializer
            ->expects($this->once())
            ->method('serialize')
            ->with($agent, 'json')
            ->will($this->returnValue($agentJson));
        $this->validateRetrieveApiCall(
            'get',
            'statements?agent='.urlencode($agentJson),
            200,
            'StatementResult',
            $statementResult
        );

        $this->assertEquals($statementResult, $this->client->getStatements($filter));
    }

    public function testGetStatementsWithVerbInStatementsFilter()
    {
        $filter = new StatementsFilter();
        $verb = new Verb();
        $verb->setId('http://adlnet.gov/expapi/verbs/attended');
        $filter->byVerb($verb);
        $statementResult = $this->createStatementResult();
        $this->validateRetrieveApiCall(
            'get',
            'statements?verb='.urlencode('http://adlnet.gov/expapi/verbs/attended'),
            200,
            'StatementResult',
            $statementResult
        );

        $this->assertEquals($statementResult, $this->client->getStatements($filter));
    }

    public function testGetNextStatements()
    {
        $moreUrl = '/xapi/statements/more/b381d8eca64a61a42c7b9b4ecc2fabb6';
        $previousStatementResult = new StatementResult();
        $previousStatementResult->setMoreUrlPath($moreUrl);
        $this->validateRetrieveApiCall(
            'get',
            $moreUrl,
            200,
            'StatementResult',
            $previousStatementResult
        );

        $statementResult = $this->client->getNextStatements($previousStatementResult);

        $this->assertInstanceOf(
            '\Xabbuh\XApi\Common\Model\StatementResultInterface',
            $statementResult
        );
    }

    public function testCreateOrUpdateStateDocument()
    {
        $document = $this->createStateDocument();

        $this->validateStoreApiCall(
            'post',
            'activities/state?activityId=activity-id&agent=agent-as-json&stateId=state-id',
            204,
            '',
            $document,
            array(array('data' => $document->getState()->getActor(), 'result' => 'agent-as-json'))
        );

        $this->client->createOrUpdateStateDocument($document);
    }

    public function testCreateOrReplaceStateDocument()
    {
        $document = $this->createStateDocument();

        $this->validateStoreApiCall(
            'put',
            'activities/state?activityId=activity-id&agent=agent-as-json&stateId=state-id',
            204,
            '',
            $document,
            array(array('data' => $document->getState()->getActor(), 'result' => 'agent-as-json'))
        );

        $this->client->createOrReplaceStateDocument($document);
    }

    public function testDeleteStateDocument()
    {
        $state = $this->createState();

        $this->validateDeleteDocumentCall(
            'activities/state?activityId=activity-id&agent=agent-as-json&stateId=state-id',
            array(array('data' => $state->getActor(), 'result' => 'agent-as-json'))
        );

        $this->client->deleteStateDocument($state);
    }

    public function testGetStateDocument()
    {
        $state = $this->createState();
        $document = new StateDocument();
        $document['x'] = 'foo';

        $this->validateRetrieveApiCall(
            'get',
            'activities/state?activityId=activity-id&agent=agent-as-json&stateId=state-id',
            200,
            'StateDocument',
            $document,
            array(array('data' => $state->getActor(), 'result' => 'agent-as-json'))
        );

        $document = $this->client->getStateDocument($state);

        $this->assertInstanceOf('Xabbuh\XApi\Common\Model\StateDocument', $document);
        $this->assertEquals($state, $document->getState());
    }

    public function testCreateOrUpdateActivityProfileDocument()
    {
        $document = $this->createActivityProfileDocument();

        $this->validateStoreApiCall(
            'post',
            'activities/profile?activityId=activity-id&profileId=profile-id',
            204,
            '',
            $document
        );

        $this->client->createOrUpdateActivityProfileDocument($document);
    }

    public function testCreateOrReplaceActivityProfileDocument()
    {
        $document = $this->createActivityProfileDocument();

        $this->validateStoreApiCall(
            'put',
            'activities/profile?activityId=activity-id&profileId=profile-id',
            204,
            '',
            $document
        );

        $this->client->createOrReplaceActivityProfileDocument($document);
    }

    public function testDeleteActivityProfileDocument()
    {
        $activityProfile = $this->createActivityProfile();

        $this->validateDeleteDocumentCall('activities/profile?activityId=activity-id&profileId=profile-id');

        $this->client->deleteActivityProfileDocument($activityProfile);
    }

    public function testGetActivityProfileDocument()
    {
        $activityProfile = $this->createActivityProfile();
        $document = new ActivityProfileDocument();
        $document['x'] = 'foo';

        $this->validateRetrieveApiCall(
            'get',
            'activities/profile?activityId=activity-id&profileId=profile-id',
            200,
            'ActivityProfileDocument',
            $document
        );

        $document = $this->client->getActivityProfileDocument($activityProfile);

        $this->assertInstanceOf('Xabbuh\XApi\Common\Model\ActivityProfileDocument', $document);
        $this->assertEquals($activityProfile, $document->getActivityProfile());
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

    private function createState()
    {
        $agent = new Agent();
        $agent->setMbox('mailto:alice@example.com');
        $activity = new Activity();
        $activity->setId('activity-id');
        $state = new State();
        $state->setActor($agent);
        $state->setActivity($activity);
        $state->setStateId('state-id');

        return $state;
    }

    private function createStateDocument()
    {
        $state = $this->createState();
        $document = new StateDocument();
        $document['x'] = 'foo';
        $document['y'] = 'bar';
        $document->setState($state);

        return $document;
    }

    private function createActivityProfile()
    {
        $activity = new Activity();
        $activity->setId('activity-id');
        $activityProfile = new ActivityProfile();
        $activityProfile->setActivity($activity);
        $activityProfile->setProfileId('profile-id');

        return $activityProfile;
    }

    private function createActivityProfileDocument()
    {
        $activityProfile = $this->createActivityProfile();
        $document = new ActivityProfileDocument();
        $document->setActivityProfile($activityProfile);

        return $document;
    }

    private function validateDeserializer($data, $type, $returnValue)
    {
        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with($data, 'Xabbuh\XApi\Common\Model\\'.$type, 'json')
            ->will($this->returnValue($returnValue));
    }

    private function validateSerializer(array $serializerMap)
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

    private function validateRequest($method, $uri, $body = null, $response = null)
    {
        $request = $this->createRequestMock($response);
        $request
            ->expects($this->once())
            ->method('send');

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

    private function validateRetrieveApiCall($method, $uri, $statusCode, $type, $transformedResult, array $serializerMap = array())
    {
        $rawResponse = 'the-server-response';
        $response = $this->createResponseMock($statusCode, $rawResponse);
        $this->validateRequest($method, $uri, null, $response);
        $this->validateSerializer($serializerMap);

        if ($statusCode < 400) {
            $this->validateDeserializer($rawResponse, $type, $transformedResult);
        }
    }

    private function validateStoreApiCall($method, $uri, $statusCode, $rawResponse, $object, array $serializerMap = array())
    {
        $rawRequest = 'the-request-body';
        $response = $this->createResponseMock($statusCode, $rawResponse);
        $this->validateRequest($method, $uri, $rawRequest, $response);
        $serializerMap[] = array('data' => $object, 'result' => $rawRequest);
        $this->validateSerializer($serializerMap);
    }

    private function validateDeleteDocumentCall($uri, array $serializerMap = array())
    {
        $response = $this->createResponseMock(204, '');
        $this->validateRequest('delete', $uri, '', $response);
        $this->validateSerializer($serializerMap);
    }
}
