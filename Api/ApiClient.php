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

/**
 * Base class for all API client classes.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
abstract class ApiClient
{
    protected $requestExecutor;

    protected $serializer;

    protected $version;

    /**
     * @param RequestExecutorInterface $requestExecutor
     * @param SerializerInterface      $serializer
     * @param string                   $version         The xAPI version
     */
    public function __construct(RequestExecutorInterface $requestExecutor, SerializerInterface $serializer, $version)
    {
        $this->requestExecutor = $requestExecutor;
        $this->serializer = $serializer;
        $this->version = $version;
    }

    public function getRequestExecutor()
    {
        return $this->requestExecutor;
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
