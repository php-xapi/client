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

use Xabbuh\XApi\Client\Api\ActivityProfileApiClient;
use Xabbuh\XApi\Client\Api\AgentProfileApiClient;
use Xabbuh\XApi\Client\Api\ApiClient;
use Xabbuh\XApi\Client\Api\StateApiClient;
use Xabbuh\XApi\Client\Api\StatementsApiClient;

/**
 * An Experience API client.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class XApiClient extends ApiClient implements XApiClientInterface
{
    /**
     * {@inheritDoc}
     */
    public function getStatementsApiClient()
    {
        return new StatementsApiClient($this->requestHandler, $this->serializer, $this->version);
    }

    /**
     * {@inheritDoc}
     */
    public function getStateApiClient()
    {
        return new StateApiClient($this->requestHandler, $this->serializer, $this->version);
    }

    /**
     * {@inheritDoc}
     */
    public function getActivityProfileApiClient()
    {
        return new ActivityProfileApiClient($this->requestHandler, $this->serializer, $this->version);
    }

    /**
     * {@inheritDoc}
     */
    public function getAgentProfileApiClient()
    {
        return new AgentProfileApiClient($this->requestHandler, $this->serializer, $this->version);
    }
}
