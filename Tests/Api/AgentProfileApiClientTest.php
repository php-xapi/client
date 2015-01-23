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
use Xabbuh\XApi\DataFixtures\DocumentFixtures;
use Xabbuh\XApi\Model\Agent;
use Xabbuh\XApi\Model\AgentProfile;
use Xabbuh\XApi\Serializer\ActorSerializer;
use Xabbuh\XApi\Serializer\DocumentDataSerializer;

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
        $this->client = new AgentProfileApiClient(
            $this->requestHandler,
            '1.0.1',
            new DocumentDataSerializer($this->serializer),
            new ActorSerializer($this->serializer)
        );
    }

    public function testCreateOrUpdateDocument()
    {
        $document = DocumentFixtures::getAgentProfileDocument();
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
            $document->getData(),
            array(array('data' => $profile->getAgent(), 'result' => 'agent-as-json'))
        );

        $this->client->createOrUpdateDocument($document);
    }

    public function testCreateOrReplaceDocument()
    {
        $document = DocumentFixtures::getAgentProfileDocument();
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
            $document->getData(),
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
        $document = DocumentFixtures::getAgentProfileDocument();
        $profile = $document->getAgentProfile();

        $this->validateRetrieveApiCall(
            'get',
            'agents/profile',
            array(
                'agent' => 'agent-as-json',
                'profileId' => 'profile-id',
            ),
            200,
            'DocumentData',
            $document->getData(),
            array(array('data' => $profile->getAgent(), 'result' => 'agent-as-json'))
        );

        $document = $this->client->getDocument($profile);

        $this->assertInstanceOf('Xabbuh\XApi\Model\AgentProfileDocument', $document);
        $this->assertEquals($profile, $document->getAgentProfile());
    }

    private function createAgentProfile()
    {
        $agent = new Agent();
        $profile = new AgentProfile('profile-id', $agent);

        return $profile;
    }
}
