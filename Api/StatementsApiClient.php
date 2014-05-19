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

use Xabbuh\XApi\Client\StatementsFilterInterface;
use Xabbuh\XApi\Common\Model\ActorInterface;
use Xabbuh\XApi\Common\Model\StatementInterface;
use Xabbuh\XApi\Common\Model\StatementResultInterface;

/**
 * Client to access the statements API of an xAPI based learning record store.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class StatementsApiClient extends ApiClient implements StatementsApiClientInterface
{
    /**
     * {@inheritDoc}
     */
    public function storeStatement(StatementInterface $statement)
    {
        if (null !== $statement->getId()) {
            return $this->doStoreStatements(
                $statement,
                'put',
                array('statementId' => $statement->getId()),
                204
            );
        } else {
            return $this->doStoreStatements($statement);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function storeStatements(array $statements)
    {
        // check that only Statements without ids will be sent to the LRS
        foreach ($statements as $statement) {
            /** @var StatementInterface $statement */

            $isStatement = is_object($statement) && $statement instanceof StatementInterface;

            if (!$isStatement || null !== $statement->getId()) {
                throw new \InvalidArgumentException('API can only handle statements without ids');
            }
        }

        return $this->doStoreStatements($statements);
    }

    /**
     * {@inheritDoc}
     */
    public function voidStatement(StatementInterface $statement, ActorInterface $actor)
    {
        return $this->storeStatement($statement->getVoidStatement($actor));
    }

    /**
     * {@inheritDoc}
     */
    public function getStatement($statementId)
    {
        return $this->doGetStatements('statements', array('statementId' => $statementId));
    }

    /**
     * {@inheritDoc}
     */
    public function getVoidedStatement($statementId)
    {
        return $this->doGetStatements('statements', array('voidedStatementId' => $statementId));
    }

    /**
     * {@inheritDoc}
     */
    public function getStatements(StatementsFilterInterface $filter = null)
    {
        $urlParameters = array();

        if (null !== $filter) {
            $urlParameters = $filter->getFilter();
        }

        // the Agent must be JSON encoded
        if (isset($urlParameters['agent'])) {
            $urlParameters['agent'] = $this->serializer->serialize(
                $urlParameters['agent'],
                'json'
            );
        }

        return $this->doGetStatements('statements', $urlParameters);
    }

    /**
     * {@inheritDoc}
     */
    public function getNextStatements(StatementResultInterface $statementResult)
    {
        return $this->doGetStatements($statementResult->getMoreUrlPath());
    }

    /**
     * @param StatementInterface|StatementInterface[] $statements
     * @param string                                  $method
     * @param string[]                                $parameters
     * @param int                                     $validStatusCode
     *
     * @return StatementInterface|StatementInterface[] The created statement(s)
     */
    private function doStoreStatements($statements, $method = 'post', $parameters = array(), $validStatusCode = 200)
    {
        $request = $this->requestHandler->createRequest(
            $method,
            'statements',
            $parameters,
            $this->serializer->serialize($statements, 'json')
        );

        $response = $this->requestHandler->executeRequest($request, array($validStatusCode));
        $statementIds = json_decode($response->getBody(true));

        if (is_array($statements)) {
            $createdStatements = array();

            foreach ($statementIds as $index => $statementId) {
                /** @var StatementInterface $statement */
                $statement = clone $statements[$index];
                $statement->setId($statementId);
                $createdStatements[] = $statement;
            }

            return $createdStatements;
        } else {
            $createdStatement = clone $statements;
            $createdStatement->setId($statementIds[0]);

            return $createdStatement;
        }
    }

    /**
     * Fetch one or more Statements.
     *
     * @param string $url           URL to request
     * @param array  $urlParameters URL parameters
     *
     * @return StatementInterface|\Xabbuh\XApi\Common\Model\StatementResultInterface
     */
    private function doGetStatements($url, array $urlParameters = array())
    {
        $request = $this->requestHandler->createRequest('get', $url, $urlParameters);
        $response = $this->requestHandler->executeRequest($request, array(200));

        if (isset($urlParameters['statementId']) || isset($urlParameters['voidedStatementId'])) {
            $class = 'Xabbuh\XApi\Common\Model\Statement';
        } else {
            $class = 'Xabbuh\XApi\Common\Model\StatementResult';
        }

        return $this->serializer->deserialize(
            $response->getBody(true),
            $class,
            'json'
        );
    }
}
