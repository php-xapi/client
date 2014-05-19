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

use JMS\Serializer\SerializerInterface;
use Xabbuh\XApi\Client\Request\HandlerInterface;

/**
 * Base class for all API client classes.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
abstract class ApiClient
{
    protected $requestHandler;

    protected $serializer;

    protected $version;

    /**
     * @param HandlerInterface         $requestHandler
     * @param SerializerInterface      $serializer
     * @param string                   $version         The xAPI version
     */
    public function __construct(HandlerInterface $requestHandler, SerializerInterface $serializer, $version)
    {
        $this->requestHandler = $requestHandler;
        $this->serializer = $serializer;
        $this->version = $version;
    }

    public function getRequestHandler()
    {
        return $this->requestHandler;
    }

    public function getSerializer()
    {
        return $this->serializer;
    }

    public function getVersion()
    {
        return $this->version;
    }
}
