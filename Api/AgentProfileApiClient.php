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

use Xabbuh\XApi\Common\Model\AgentProfileDocumentInterface;
use Xabbuh\XApi\Common\Model\AgentProfileInterface;

/**
 * Client to access the agent profile API of an xAPI based learning record
 * store.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class AgentProfileApiClient extends DocumentApiClient implements AgentProfileApiClientInterface
{
    /**
     * {@inheritDoc}
     */
    public function createOrUpdateDocument(AgentProfileDocumentInterface $document)
    {
        $profile = $document->getAgentProfile();
        $this->doStoreDocument('post', 'agents/profile', array(
            'agent' => $this->serializer->serialize($profile->getAgent(), 'json'),
            'profileId' => $profile->getProfileId(),
        ), $document);
    }

    /**
     * {@inheritDoc}
     */
    public function createOrReplaceDocument(AgentProfileDocumentInterface $document)
    {
        $profile = $document->getAgentProfile();
        $this->doStoreDocument('put', 'agents/profile', array(
            'agent' => $this->serializer->serialize($profile->getAgent(), 'json'),
            'profileId' => $profile->getProfileId(),
        ), $document);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteDocument(AgentProfileInterface $profile)
    {
        $this->doDeleteDocument('agents/profile', array(
            'agent' => $this->serializer->serialize($profile->getAgent(), 'json'),
            'profileId' => $profile->getProfileId(),
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getDocument(AgentProfileInterface $profile)
    {
        /** @var \Xabbuh\XApi\Common\Model\AgentProfileDocument $document */
        $document = $this->doGetDocument(
            'Xabbuh\XApi\Common\Model\AgentProfileDocument',
            'agents/profile',
            array(
                'agent' => $this->serializer->serialize($profile->getAgent(), 'json'),
                'profileId' => $profile->getProfileId(),
            )
        );
        $document->setAgentProfile($profile);

        return $document;
    }
}
