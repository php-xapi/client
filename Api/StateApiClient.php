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

use Xabbuh\XApi\Common\Model\StateDocumentInterface;
use Xabbuh\XApi\Common\Model\StateInterface;

/**
 * Client to access the state API of an xAPI based learning record store.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class StateApiClient extends DocumentApiClient implements StateApiClientInterface
{
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
            'agent' => $this->serializer->serialize($state->getActor(), 'json'),
            'stateId' => $state->getStateId(),
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getDocument(StateInterface $state)
    {
        /** @var \Xabbuh\XApi\Common\Model\StateDocument $document */
        $document = $this->doGetDocument('Xabbuh\XApi\Common\Model\StateDocument', 'activities/state', array(
            'activityId' => $state->getActivity()->getId(),
            'agent' => $this->serializer->serialize($state->getActor(), 'json'),
            'stateId' => $state->getStateId(),
        ));
        $document->setState($state);

        return $document;
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
                'agent' => $this->serializer->serialize($state->getActor(), 'json'),
                'stateId' => $state->getStateId(),
            ),
            $document
        );
    }
}
