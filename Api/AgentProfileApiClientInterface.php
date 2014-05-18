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
interface AgentProfileApiClientInterface
{
    /**
     * Returns the xAPI version.
     *
     * @return string The xAPI version
     */
    public function getVersion();

    /**
     * Stores a document for an agent profile. Updates an existing document for
     * this agent profile if one exists.
     *
     * @param AgentProfileDocumentInterface $document The document to store
     */
    public function createOrUpdateDocument(AgentProfileDocumentInterface $document);

    /**
     * Stores a document for an agent profile. Replaces any existing document
     * for this agent profile.
     *
     * @param AgentProfileDocumentInterface $document The document to store
     */
    public function createOrReplaceDocument(AgentProfileDocumentInterface $document);

    /**
     * Deletes a document stored for the given agent profile.
     *
     * @param AgentProfileInterface $profile The agent profile
     */
    public function deleteDocument(AgentProfileInterface $profile);

    /**
     * Returns the document for an agent profile.
     *
     * @param AgentProfileInterface $profile The agent profile to request the
     *                                       document for
     *
     * @return AgentProfileDocumentInterface The document
     */
    public function getDocument(AgentProfileInterface $profile);
}
