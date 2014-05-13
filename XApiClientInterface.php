<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\XApi\Client;

use Xabbuh\XApi\Common\Exception\ConflictException;
use Xabbuh\XApi\Common\Exception\NotFoundException;
use Xabbuh\XApi\Common\Exception\XApiException;
use Xabbuh\XApi\Common\Model\ActorInterface;
use Xabbuh\XApi\Common\Model\StateDocumentInterface;
use Xabbuh\XApi\Common\Model\StateInterface;
use Xabbuh\XApi\Common\Model\StatementInterface;
use Xabbuh\XApi\Common\Model\StatementResultInterface;

/**
 * An Experience API client.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
interface XApiClientInterface
{
    /**
     * Stores a single {@link \Xabbuh\XApi\Common\Model\StatementInterface Statement}.
     *
     * @param StatementInterface $statement The Statement to store
     *
     * @return StatementInterface The Statement as it has been stored in the
     *                            remote LRS, this is not necessarily the same
     *                            object that was passed to storeStatement()
     *
     * @throws ConflictException if a Statement with the given id already exists
     *                           and the given Statement does not match the
     *                           stored Statement
     * @throws XApiException     for all other xAPI related problems
     */
    public function storeStatement(StatementInterface $statement);

    /**
     * Stores a collection of {@link \Xabbuh\XApi\Common\Model\StatementInterface Statements}.
     *
     * @param StatementInterface[] $statements The statements to store
     *
     * @return StatementInterface[] The stored Statements
     *
     * @throws \InvalidArgumentException if a given object is no Statement or
     *                                   if one of the Statements has an id
     * @throws XApiException             for all other xAPI related problems
     */
    public function storeStatements(array $statements);

    /**
     * Marks a {@link \Xabbuh\XApi\Common\Model\StatementInterface Statement}
     * as voided.
     *
     * @param StatementInterface $statement The Statement to void
     * @param ActorInterface     $actor     The Actor voiding the given Statement
     *
     * @return StatementInterface The Statement sent to the remote LRS to void
     *                            the given Statement
     *
     * @throws XApiException for all other xAPI related problems
     */
    public function voidStatement(StatementInterface $statement, ActorInterface $actor);

    /**
     * Retrieves a single {@link \Xabbuh\XApi\Common\Model\StatementInterface Statement}.
     *
     * @param string $statementId The Statement id
     *
     * @return StatementInterface The Statement
     *
     * @throws NotFoundException if no statement with the given id could be found
     * @throws XApiException     for all other xAPI related problems
     */
    public function getStatement($statementId);

    /**
     * Retrieves a voided {@link \Xabbuh\XApi\Common\Model\StatementInterface Statement}.
     *
     * @param string $statementId The id of the voided Statement
     *
     * @return StatementInterface The voided Statement
     *
     * @throws NotFoundException if no statement with the given id could be found
     * @throws XApiException     for all other xAPI related problems
     */
    public function getVoidedStatement($statementId);

    /**
     * Retrieves a collection of {@link \Xabbuh\XApi\Common\Model\StatementInterface Statements}.
     *
     * @param StatementsFilterInterface $filter Optional Statements filter
     *
     * @return StatementResultInterface The {@link \Xabbuh\XApi\Common\Model\StatementResult}
     *
     * @throws XApiException in case of any problems related to the xAPI
     */
    public function getStatements(StatementsFilterInterface $filter = null);

    /**
     * Returns the next {@link \Xabbuh\XApi\Common\Model\StatementInterface Statements}
     * for a limited Statement result.
     *
     * @param StatementResultInterface $statementResult The former StatementResult
     *
     * @return StatementResultInterface The {@link \Xabbuh\XApi\Common\Model\StatementResult}
     *
     * @throws XApiException in case of any problems related to the xAPI
     */
    public function getNextStatements(StatementResultInterface $statementResult);

    /**
     * Stores a document for a state. Updates an existing document for this
     * state if one exists.
     *
     * @param StateDocumentInterface $document The document to store
     */
    public function createOrUpdateStateDocument(StateDocumentInterface $document);

    /**
     * Stores a document for a state. Replaces any existing document for this
     * state.
     *
     * @param StateDocumentInterface $document The document to store
     */
    public function createOrReplaceStateDocument(StateDocumentInterface $document);

    /**
     * Deletes a document stored for the given state.
     *
     * @param StateInterface $state The state
     */
    public function deleteStateDocument(StateInterface $state);

    /**
     * Returns the document for a state.
     *
     * @param StateInterface $state The state to request the document for
     *
     * @return StateDocumentInterface The document
     */
    public function getStateDocument(StateInterface $state);
}
