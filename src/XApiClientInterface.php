<?php

/*
 * This file is part of the XabbuhXApiClient package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\XApi\Client;

use Xabbuh\XApi\Common\Model\StatementInterface;

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
     * @return string The id of the created statement
     */
    public function storeStatement(StatementInterface $statement);

    /**
     * Retrieves a single {@link \Xabbuh\XApi\Common\Model\StatementInterface Statement}.
     *
     * @param string $statementId The Statement id
     *
     * @return StatementInterface The Statement
     */
    public function getStatement($statementId);

    /**
     * Retrieves a collection of {@link \Xabbuh\XApi\Common\Model\StatementInterface Statements}.
     *
     * @return \Xabbuh\XApi\Common\Model\StatementResultInterface The {@link \Xabbuh\XApi\Common\Model\StatementResult}
     */
    public function getStatements();
}
