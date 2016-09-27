<?php

namespace spec\Xabbuh\XApi\Client\Request;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;
use Guzzle\Service\ClientInterface;
use PhpSpec\ObjectBehavior;

class HandlerSpec extends ObjectBehavior
{
    function let(ClientInterface $client)
    {
        $this->beConstructedWith($client, '1.0.1');
    }

    function it_throws_an_exception_if_a_request_is_created_with_an_invalid_method()
    {
        $this->shouldThrow('\InvalidArgumentException')->during('createRequest', array('options', '/xapi/statements'));
    }

    function it_sets_the_experience_api_version_header_and_the_content_type_header_when_creating_a_request(ClientInterface $client, RequestInterface $request)
    {
        $client->get('/xapi/statements')->willReturn($request);
        $request->addHeader('X-Experience-API-Version', '1.0.1')->shouldBeCalled();
        $request->addHeader('Content-Type', 'application/json')->shouldBeCalled();
        $request->setAuth(null, null)->shouldBeCalled();

        $this->createRequest('get', '/xapi/statements');
    }

    function it_returns_get_request_created_by_the_http_client(ClientInterface $client, RequestInterface $request)
    {
        $client->get('/xapi/statements')->willReturn($request);

        $this->createRequest('get', '/xapi/statements')->shouldReturn($request);
    }

    function it_returns_post_request_created_by_the_http_client(ClientInterface $client, RequestInterface $request)
    {
        $client->post('/xapi/statements', null, 'body')->willReturn($request);

        $this->createRequest('post', '/xapi/statements', array(), 'body')->shouldReturn($request);
    }

    function it_returns_put_request_created_by_the_http_client(ClientInterface $client, RequestInterface $request)
    {
        $client->put('/xapi/statements', null, 'body')->willReturn($request);

        $this->createRequest('put', '/xapi/statements', array(), 'body')->shouldReturn($request);
    }

    function it_returns_delete_request_created_by_the_http_client(ClientInterface $client, RequestInterface $request)
    {
        $client->delete('/xapi/statements')->willReturn($request);

        $this->createRequest('delete', '/xapi/statements')->shouldReturn($request);
    }

    function it_throws_an_access_denied_exception_when_a_401_status_code_is_returned(RequestInterface $request, Response $response)
    {
        $request->send()->willReturn($response);
        $response->getStatusCode()->willReturn(401);
        $response->getBody(true)->willReturn('body');

        $this->shouldThrow('Xabbuh\XApi\Common\Exception\AccessDeniedException')->during('executeRequest', array($request, array(200)));
    }

    function it_throws_an_access_denied_exception_when_a_403_status_code_is_returned(RequestInterface $request, Response $response)
    {
        $request->send()->willReturn($response);
        $response->getStatusCode()->willReturn(403);
        $response->getBody(true)->willReturn('body');

        $this->shouldThrow('Xabbuh\XApi\Common\Exception\AccessDeniedException')->during('executeRequest', array($request, array(200)));
    }

    function it_throws_a_not_found_exception_when_a_404_status_code_is_returned(RequestInterface $request, Response $response)
    {
        $request->send()->willReturn($response);
        $response->getStatusCode()->willReturn(404);
        $response->getBody(true)->willReturn('body');

        $this->shouldThrow('Xabbuh\XApi\Common\Exception\NotFoundException')->during('executeRequest', array($request, array(200)));
    }

    function it_throws_a_conflict_exception_when_a_409_status_code_is_returned(RequestInterface $request, Response $response)
    {
        $request->send()->willReturn($response);
        $response->getStatusCode()->willReturn(409);
        $response->getBody(true)->willReturn('body');

        $this->shouldThrow('Xabbuh\XApi\Common\Exception\ConflictException')->during('executeRequest', array($request, array(200)));
    }

    function it_throws_an_xapi_exception_when_an_unexpected_status_code_is_returned(RequestInterface $request, Response $response)
    {
        $request->send()->willReturn($response);
        $response->getStatusCode()->willReturn(204);
        $response->getBody(true)->willReturn('body');

        $this->shouldThrow('Xabbuh\XApi\Common\Exception\XApiException')->during('executeRequest', array($request, array(200)));
    }

    function it_returns_the_response_on_success(RequestInterface $request, Response $response)
    {
        $request->send()->willReturn($response);
        $response->getStatusCode()->willReturn(200);
        $response->getBody(true)->willReturn('body');

        $this->executeRequest($request, array(200))->shouldReturn($response);
    }
}
