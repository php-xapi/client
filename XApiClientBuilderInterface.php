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

/**
 * xAPI client builder.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
interface XApiClientBuilderInterface
{
    /**
     * Sets the LRS base URL.
     *
     * @param string $baseUrl The base url
     *
     * @return XApiClientBuilderInterface The builder
     */
    public function setBaseUrl($baseUrl);

    /**
     * Sets the xAPI version.
     *
     * @param string $version The version to use
     *
     * @return XApiClientBuilderInterface The builder
     */
    public function setVersion($version);

    /**
     * Sets HTTP authentication credentials.
     *
     * @param string $username The username
     * @param string $password The password
     *
     * @return XApiClientBuilderInterface The builder
     */
    public function setAuth($username, $password);

    /**
     * Sets OAuth credentials.
     *
     * @param string $consumerKey    The consumer key
     * @param string $consumerSecret The consumer secret
     * @param string $token          The token
     * @param string $tokenSecret    The secret token
     *
     * @return XApiClientBuilderInterface The builder
     */
    public function setOAuthCredentials($consumerKey, $consumerSecret, $token, $tokenSecret);

    /**
     * Builds the xAPI client.
     *
     * @return XApiClientInterface The xAPI client
     */
    public function build();
}
