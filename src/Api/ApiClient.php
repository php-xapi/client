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

/**
 * Base class for all API client classes.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
abstract class ApiClient
{
    protected $requestHandler;

    protected $version;

    /**
     * @param HandlerInterface $requestHandler The HTTP request handler
     * @param string           $version        The xAPI version
     */
    public function __construct(HandlerInterface $requestHandler, $version)
    {
        $this->requestHandler = $requestHandler;
        $this->version = $version;
    }

    public function getRequestHandler()
    {
        return $this->requestHandler;
    }

    public function getVersion()
    {
        return $this->version;
    }
}
