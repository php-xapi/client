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
use Xabbuh\XApi\Common\Serializer\ActorSerializerInterface;
use Xabbuh\XApi\Common\Serializer\DocumentSerializerInterface;
use Xabbuh\XApi\Model\StateDocumentInterface;
use Xabbuh\XApi\Model\StateInterface;

/**
 * Client to access the state API of an xAPI based learning record store.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class StateApiClient extends DocumentApiClient implements StateApiClientInterface
{
    /**
     * @var \Xabbuh\XApi\Common\Serializer\ActorSerializerInterface
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
    public function createOrUpdateDocument(StateDocumentInterface $document)
    {
        $this->doStoreStateDocument('post', $document);
    }

    /**
     * {@inheritDoc}
     */
    public function createOrReplaceDocument(StateDocumentInterface $document)
    {
        $this->doStoreStateDocument('put', $document);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteDocument(StateInterface $state)
    {
        $this->doDeleteDocument('activities/state', array(
            'activityId' => $state->getActivity()->getId(),
            'agent' => $this->actorSerializer->serializeActor($state->getActor()),
            'stateId' => $state->getStateId(),
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getDocument(StateInterface $state)
    {
        /** @var \Xabbuh\XApi\Model\StateDocument $document */
        $document = $this->doGetDocument('activities/state', array(
            'activityId' => $state->getActivity()->getId(),
            'agent' => $this->actorSerializer->serializeActor($state->getActor()),
            'stateId' => $state->getStateId(),
        ));
        $document->setState($state);

        return $document;
    }

    /**
     * {@inheritDoc}
     */
    protected function deserializeDocument($serializedDocument)
    {
        return $this->documentSerializer->deserializeStateDocument($serializedDocument);
    }

    /**
     * Stores a state document.
     *
     * @param string                 $method   HTTP method to use
     * @param StateDocumentInterface $document The document to store
     */
    private function doStoreStateDocument($method, StateDocumentInterface $document)
    {
        $state = $document->getState();
        $this->doStoreDocument(
            $method,
            'activities/state',
            array(
                'activityId' => $state->getActivity()->getId(),
                'agent' => $this->actorSerializer->serializeActor($state->getActor()),
                'stateId' => $state->getStateId(),
            ),
            $document
        );
    }
}
