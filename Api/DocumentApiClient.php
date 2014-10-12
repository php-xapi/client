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
use Xabbuh\XApi\Serializer\DocumentSerializerInterface;
use Xabbuh\XApi\Model\DocumentInterface;

/**
 * Base class for the document API classes.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
abstract class DocumentApiClient extends ApiClient
{
    /**
     * @var DocumentSerializerInterface
     */
    protected $documentSerializer;

    /**
     * @param HandlerInterface            $requestHandler     The HTTP request handler
     * @param string                      $version            The xAPI version
     * @param DocumentSerializerInterface $documentSerializer The document serializer
     */
    public function __construct(HandlerInterface $requestHandler, $version, DocumentSerializerInterface $documentSerializer)
    {
        parent::__construct($requestHandler, $version);
        $this->documentSerializer = $documentSerializer;
    }

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
            $this->documentSerializer->serializeDocument($document)
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
     * @param string $uri           The endpoint URI
     * @param array  $urlParameters The URL parameters
     *
     * @return DocumentInterface The document
     */
    protected function doGetDocument($uri, array $urlParameters)
    {
        $request = $this->requestHandler->createRequest('get', $uri, $urlParameters);
        $response = $this->requestHandler->executeRequest($request, array(200));
        $document = $this->deserializeDocument($response->getBody(true));

        return $document;
    }

    abstract protected function deserializeDocument($serializedDocument);
}
