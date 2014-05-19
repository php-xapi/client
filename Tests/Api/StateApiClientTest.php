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

use Xabbuh\XApi\Client\Api\StateApiClient;
use Xabbuh\XApi\Common\Model\Activity;
use Xabbuh\XApi\Common\Model\Agent;
use Xabbuh\XApi\Common\Model\State;
use Xabbuh\XApi\Common\Model\StateDocument;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class StateApiClientTest extends ApiClientTest
{
    /**
     * @var StateApiClient
     */
    private $client;

    protected function setUp()
    {
        parent::setUp();
        $this->client = new StateApiClient($this->requestHandler, $this->serializer, '1.0.1');
    }

    public function testCreateOrUpdateDocument()
    {
        $document = $this->createStateDocument();

        $this->validateStoreApiCall(
            'post',
            'activities/state',
            array(
                'activityId' => 'activity-id',
                'agent' => 'agent-as-json',
                'stateId' => 'state-id',
            ),
            204,
            '',
            $document,
            array(array('data' => $document->getState()->getActor(), 'result' => 'agent-as-json'))
        );

        $this->client->createOrUpdateDocument($document);
    }

    public function testCreateOrReplaceDocument()
    {
        $document = $this->createStateDocument();

        $this->validateStoreApiCall(
            'put',
            'activities/state',
            array(
                'activityId' => 'activity-id',
                'agent' => 'agent-as-json',
                'stateId' => 'state-id',
            ),
            204,
            '',
            $document,
            array(array('data' => $document->getState()->getActor(), 'result' => 'agent-as-json'))
        );

        $this->client->createOrReplaceDocument($document);
    }

    public function testDeleteDocument()
    {
        $state = $this->createState();

        $this->validateDeleteDocumentCall(
            'activities/state',
            array(
                'activityId' => 'activity-id',
                'agent' => 'agent-as-json',
                'stateId' => 'state-id',
            ),
            array(array('data' => $state->getActor(), 'result' => 'agent-as-json'))
        );

        $this->client->deleteDocument($state);
    }

    public function testGetStateDocument()
    {
        $state = $this->createState();
        $document = new StateDocument();
        $document['x'] = 'foo';

        $this->validateRetrieveApiCall(
            'get',
            'activities/state',
            array(
                'activityId' => 'activity-id',
                'agent' => 'agent-as-json',
                'stateId' => 'state-id',
            ),
            200,
            'StateDocument',
            $document,
            array(array('data' => $state->getActor(), 'result' => 'agent-as-json'))
        );

        $document = $this->client->getDocument($state);

        $this->assertInstanceOf('Xabbuh\XApi\Common\Model\StateDocument', $document);
        $this->assertEquals($state, $document->getState());
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
}
