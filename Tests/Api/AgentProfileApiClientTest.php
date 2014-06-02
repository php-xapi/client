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

use Xabbuh\XApi\Client\Api\AgentProfileApiClient;
use Xabbuh\XApi\Common\Model\Agent;
use Xabbuh\XApi\Common\Model\AgentProfile;
use Xabbuh\XApi\Common\Model\AgentProfileDocument;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class AgentProfileApiClientTest extends ApiClientTest
{
    /**
     * @var AgentProfileApiClient
     */
    private $client;

    protected function setUp()
    {
        parent::setUp();
        $this->client = new AgentProfileApiClient($this->requestHandler, $this->serializerRegistry, '1.0.1');
    }

    public function testCreateOrUpdateDocument()
    {
        $document = $this->createAgentProfileDocument();
        $profile = $document->getAgentProfile();

        $this->validateStoreApiCall(
            'post',
            'agents/profile',
            array(
                'agent' => 'agent-as-json',
                'profileId' => 'profile-id',
            ),
            204,
            '',
            $document,
            array(array('data' => $profile->getAgent(), 'result' => 'agent-as-json'))
        );

        $this->client->createOrUpdateDocument($document);
    }

    public function testCreateOrReplaceDocument()
    {
        $document = $this->createAgentProfileDocument();
        $profile = $document->getAgentProfile();

        $this->validateStoreApiCall(
            'put',
            'agents/profile',
            array(
                'agent' => 'agent-as-json',
                'profileId' => 'profile-id',
            ),
            204,
            '',
            $document,
            array(array('data' => $profile->getAgent(), 'result' => 'agent-as-json'))
        );

        $this->client->createOrReplaceDocument($document);
    }

    public function testDeleteDocument()
    {
        $profile = $this->createAgentProfile();

        $this->validateDeleteDocumentCall(
            'agents/profile',
            array(
                'agent' => 'agent-as-json',
                'profileId' => 'profile-id',
            ),
            array(array('data' => $profile->getAgent(), 'result' => 'agent-as-json'))
        );

        $this->client->deleteDocument(
            $profile
        );
    }

    public function testGetDocument()
    {
        $profile = $this->createAgentProfile();
        $document = new AgentProfileDocument();
        $document['x'] = 'foo';

        $this->validateRetrieveApiCall(
            'get',
            'agents/profile',
            array(
                'agent' => 'agent-as-json',
                'profileId' => 'profile-id',
            ),
            200,
            'AgentProfileDocument',
            $document,
            array(array('data' => $profile->getAgent(), 'result' => 'agent-as-json'))
        );

        $document = $this->client->getDocument($profile);

        $this->assertInstanceOf('Xabbuh\XApi\Common\Model\AgentProfileDocument', $document);
        $this->assertEquals($profile, $document->getAgentProfile());
    }

    private function createAgentProfile()
    {
        $agent = new Agent();
        $profile = new AgentProfile();
        $profile->setAgent($agent);
        $profile->setProfileId('profile-id');

        return $profile;
    }

    private function createAgentProfileDocument()
    {
        $profile = $this->createAgentProfile();
        $document = new AgentProfileDocument();
        $document->setAgentProfile($profile);

        return $document;
    }
}
