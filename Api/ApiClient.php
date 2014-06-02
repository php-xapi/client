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
use Xabbuh\XApi\Common\Serializer\SerializerRegistryInterface;

/**
 * Base class for all API client classes.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
abstract class ApiClient
{
    protected $requestHandler;

    protected $serializerRegistry;

    protected $version;

    /**
     * @param HandlerInterface            $requestHandler
     * @param SerializerRegistryInterface $serializerRegistry
     * @param string                      $version            The xAPI version
     */
    public function __construct(HandlerInterface $requestHandler, SerializerRegistryInterface $serializerRegistry, $version)
    {
        $this->requestHandler = $requestHandler;
        $this->serializerRegistry = $serializerRegistry;
        $this->version = $version;
    }

    public function getRequestHandler()
    {
        return $this->requestHandler;
    }

    public function getSerializerRegistry()
    {
        return $this->serializerRegistry;
    }

    public function getVersion()
    {
        return $this->version;
    }
}
