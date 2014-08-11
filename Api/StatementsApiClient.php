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
use Xabbuh\XApi\Client\StatementsFilterInterface;
use Xabbuh\XApi\Common\Serializer\ActorSerializerInterface;
use Xabbuh\XApi\Common\Serializer\StatementResultSerializerInterface;
use Xabbuh\XApi\Common\Serializer\StatementSerializerInterface;
use Xabbuh\XApi\Model\ActorInterface;
use Xabbuh\XApi\Model\StatementInterface;
use Xabbuh\XApi\Model\StatementResultInterface;

/**
 * Client to access the statements API of an xAPI based learning record store.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class StatementsApiClient extends ApiClient implements StatementsApiClientInterface
{
    /**
     * @var \Xabbuh\XApi\Common\Serializer\StatementSerializerInterface
     */
    private $statementSerializer;

    /**
     * @var \Xabbuh\XApi\Common\Serializer\StatementResultSerializerInterface
     */
    private $statementResultSerializer;

    /**
     * @var \Xabbuh\XApi\Common\Serializer\ActorSerializerInterface
     */
    private $actorSerializer;

    /**
     * @param HandlerInterface                   $requestHandler            The HTTP request handler
     * @param string                             $version                   The xAPI version
     * @param StatementSerializerInterface       $statementSerializer       The statement serializer
     * @param StatementResultSerializerInterface $statementResultSerializer The statement result serializer
     * @param ActorSerializerInterface           $actorSerializer           The actor serializer
     */
    public function __construct(
        HandlerInterface $requestHandler,
        $version,
        StatementSerializerInterface $statementSerializer,
        StatementResultSerializerInterface $statementResultSerializer,
        ActorSerializerInterface $actorSerializer
    ) {
        parent::__construct($requestHandler, $version);
        $this->statementSerializer = $statementSerializer;
        $this->statementResultSerializer = $statementResultSerializer;
        $this->actorSerializer = $actorSerializer;
    }

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
            $urlParameters['agent'] = $this->actorSerializer->serializeActor($urlParameters['agent']);
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
        if (is_array($statements)) {
            $serializedStatements = $this->statementSerializer->serializeStatements($statements);
        } else {
            $serializedStatements = $this->statementSerializer->serializeStatement($statements);
        }

        $request = $this->requestHandler->createRequest(
            $method,
            'statements',
            $parameters,
            $serializedStatements
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

            if (200 === $validStatusCode) {
                $createdStatement->setId($statementIds[0]);
            }

            return $createdStatement;
        }
    }

    /**
     * Fetch one or more Statements.
     *
     * @param string $url           URL to request
     * @param array  $urlParameters URL parameters
     *
     * @return StatementInterface|\Xabbuh\XApi\Model\StatementResultInterface
     */
    private function doGetStatements($url, array $urlParameters = array())
    {
        $request = $this->requestHandler->createRequest('get', $url, $urlParameters);
        $response = $this->requestHandler->executeRequest($request, array(200));

        if (isset($urlParameters['statementId']) || isset($urlParameters['voidedStatementId'])) {
            return $this->statementSerializer->deserializeStatement($response->getBody(true));
        } else {
            return $this->statementResultSerializer->deserializeStatementResult($response->getBody(true));
        }
    }
}
