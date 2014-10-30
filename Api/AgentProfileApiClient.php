<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\XApi\Client\Api;

use Xabbuh\XApi\Client\Request\HandlerInterface;
use Xabbuh\XApi\Serializer\ActorSerializerInterface;
use Xabbuh\XApi\Serializer\DocumentSerializerInterface;
use Xabbuh\XApi\Model\AgentProfile;
use Xabbuh\XApi\Model\AgentProfileDocument;

/**
 * Client to access the agent profile API of an xAPI based learning record
 * store.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class AgentProfileApiClient extends DocumentApiClient implements AgentProfileApiClientInterface
{
    /**
     * @var ActorSerializerInterface
     */
    private $actorSerializer;

    /**
     * @param HandlerInterface            $requestHandler     The HTTP request handler
     * @param string                      $version            The xAPI version
     * @param DocumentSerializerInterface $documentSerializer The document serializer
     * @param ActorSerializerInterface    $actorSerializer    The actor serializer
     */
    public function __construct(
        HandlerInterface $requestHandler,
        $version,
        DocumentSerializerInterface $documentSerializer,
        ActorSerializerInterface $actorSerializer
    ) {
        parent::__construct($requestHandler, $version, $documentSerializer);
        $this->actorSerializer = $actorSerializer;
    }

    /**
     * {@inheritDoc}
     */
    public function createOrUpdateDocument(AgentProfileDocument $document)
    {
        $profile = $document->getAgentProfile();
        $this->doStoreDocument('post', 'agents/profile', array(
            'agent' => $this->actorSerializer->serializeActor($profile->getAgent()),
            'profileId' => $profile->getProfileId(),
        ), $document);
    }

    /**
     * {@inheritDoc}
     */
    public function createOrReplaceDocument(AgentProfileDocument $document)
    {
        $profile = $document->getAgentProfile();
        $this->doStoreDocument('put', 'agents/profile', array(
            'agent' => $this->actorSerializer->serializeActor($profile->getAgent()),
            'profileId' => $profile->getProfileId(),
        ), $document);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteDocument(AgentProfile $profile)
    {
        $this->doDeleteDocument('agents/profile', array(
            'agent' => $this->actorSerializer->serializeActor($profile->getAgent()),
            'profileId' => $profile->getProfileId(),
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getDocument(AgentProfile $profile)
    {
        /** @var \Xabbuh\XApi\Model\AgentProfileDocument $document */
        $document = $this->doGetDocument('agents/profile', array(
            'agent' => $this->actorSerializer->serializeActor($profile->getAgent()),
            'profileId' => $profile->getProfileId(),
        ));
        $document->setAgentProfile($profile);

        return $document;
    }

    /**
     * {@inheritDoc}
     */
    protected function deserializeDocument($serializedDocument)
    {
        return $this->documentSerializer->deserializeAgentProfileDocument($serializedDocument);
    }
}
