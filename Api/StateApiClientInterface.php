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

use Xabbuh\XApi\Model\StateDocumentInterface;
use Xabbuh\XApi\Model\StateInterface;

/**
 * Client to access the state API of an xAPI based learning record store.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
interface StateApiClientInterface
{
    /**
     * Returns the xAPI version.
     *
     * @return string The xAPI version
     */
    public function getVersion();

    /**
     * Stores a document for a state. Updates an existing document for this
     * state if one exists.
     *
     * @param StateDocumentInterface $document The document to store
     */
    public function createOrUpdateDocument(StateDocumentInterface $document);

    /**
     * Stores a document for a state. Replaces any existing document for this
     * state.
     *
     * @param StateDocumentInterface $document The document to store
     */
    public function createOrReplaceDocument(StateDocumentInterface $document);

    /**
     * Deletes a document stored for the given state.
     *
     * @param StateInterface $state The state
     */
    public function deleteDocument(StateInterface $state);

    /**
     * Returns the document for a state.
     *
     * @param StateInterface $state The state to request the document for
     *
     * @return StateDocumentInterface The document
     */
    public function getDocument(StateInterface $state);
}
