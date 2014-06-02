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

use Xabbuh\XApi\Common\Model\ActivityProfileDocumentInterface;
use Xabbuh\XApi\Common\Model\ActivityProfileInterface;

/**
 * Client to access the activity profile API of an xAPI based learning record
 * store.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ActivityProfileApiClient extends DocumentApiClient implements ActivityProfileApiClientInterface
{
    /**
     * {@inheritDoc}
     */
    public function createOrUpdateDocument(ActivityProfileDocumentInterface $document)
    {
        $this->doStoreActivityProfileDocument('post', $document);
    }

    /**
     * {@inheritDoc}
     */
    public function createOrReplaceDocument(ActivityProfileDocumentInterface $document)
    {
        $this->doStoreActivityProfileDocument('put', $document);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteDocument(ActivityProfileInterface $profile)
    {
        $this->doDeleteDocument('activities/profile', array(
            'activityId' => $profile->getActivity()->getId(),
            'profileId' => $profile->getProfileId(),
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getDocument(ActivityProfileInterface $profile)
    {
        /** @var \Xabbuh\XApi\Common\Model\ActivityProfileDocument $document */
        $document = $this->doGetDocument('activities/profile', array(
            'activityId' => $profile->getActivity()->getId(),
            'profileId' => $profile->getProfileId(),
        ));
        $document->setActivityProfile($profile);

        return $document;
    }

    /**
     * {@inheritDoc}
     */
    protected function deserializeDocument($serializedDocument)
    {
        return $this
            ->serializerRegistry
            ->getDocumentSerializer()
            ->deserializeActivityProfileDocument($serializedDocument);
    }

    /**
     * Stores a state document.
     *
     * @param string                           $method   HTTP method to use
     * @param ActivityProfileDocumentInterface $document The document to store
     */
    private function doStoreActivityProfileDocument($method, ActivityProfileDocumentInterface $document)
    {
        $profile = $document->getActivityProfile();
        $this->doStoreDocument(
            $method,
            'activities/profile',
            array(
                'activityId' => $profile->getActivity()->getId(),
                'profileId' => $profile->getProfileId(),
            ),
            $document
        );
    }
}
