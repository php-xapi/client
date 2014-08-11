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

use Xabbuh\XApi\Model\ActivityProfileDocumentInterface;
use Xabbuh\XApi\Model\ActivityProfileInterface;

/**
 * Client to access the activity profile API of an xAPI based learning record
 * store.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
interface ActivityProfileApiClientInterface
{
    /**
     * Returns the xAPI version.
     *
     * @return string The xAPI version
     */
    public function getVersion();

    /**
     * Stores a document for an activity profile. Updates an existing document
     * for this activity profile if one exists.
     *
     * @param ActivityProfileDocumentInterface $document The document to store
     */
    public function createOrUpdateDocument(ActivityProfileDocumentInterface $document);

    /**
     * Stores a document for an activity profile. Replaces any existing document
     * for this activity profile.
     *
     * @param ActivityProfileDocumentInterface $document The document to store
     */
    public function createOrReplaceDocument(ActivityProfileDocumentInterface $document);

    /**
     * Deletes a document stored for the given activity profile.
     *
     * @param ActivityProfileInterface $profile The activity profile
     */
    public function deleteDocument(ActivityProfileInterface $profile);

    /**
     * Returns the document for an activity profile.
     *
     * @param ActivityProfileInterface $profile The activity profile to request
     *                                          the document for
     *
     * @return ActivityProfileDocumentInterface The document
     */
    public function getDocument(ActivityProfileInterface $profile);
}
