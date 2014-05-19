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

use Xabbuh\XApi\Common\Model\DocumentInterface;

/**
 * Base class for the document API classes.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
abstract class DocumentApiClient extends ApiClient
{
    /**
     * Stores a document.
     *
     * @param string            $method        HTTP method to use
     * @param string            $uri           Endpoint URI
     * @param array             $urlParameters URL parameters
     * @param DocumentInterface $document      The document to store
     */
    protected function doStoreDocument($method, $uri, $urlParameters, DocumentInterface $document)
    {
        $request = $this->requestHandler->createRequest(
            $method,
            $uri,
            $urlParameters,
            $this->serializer->serialize($document, 'json')
        );
        $this->requestHandler->executeRequest($request, array(204));
    }

    /**
     * Deletes a document.
     *
     * @param string $uri           The endpoint URI
     * @param array  $urlParameters The URL parameters
     */
    protected function doDeleteDocument($uri, array $urlParameters)
    {
        $request = $this->requestHandler->createRequest('delete', $uri, $urlParameters);
        $this->requestHandler->executeRequest($request, array(204));
    }

    /**
     * Returns a document.
     *
     * @param string $type          The document type
     * @param string $uri           The endpoint URI
     * @param array  $urlParameters The URL parameters
     *
     * @return \Xabbuh\XApi\Common\Model\DocumentInterface The document
     */
    protected function doGetDocument($type, $uri, array $urlParameters)
    {
        $request = $this->requestHandler->createRequest('get', $uri, $urlParameters);
        $response = $this->requestHandler->executeRequest($request, array(200));
        $document = $this->serializer->deserialize($response->getBody(true), $type, 'json');

        return $document;
    }
}
