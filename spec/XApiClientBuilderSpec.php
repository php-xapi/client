<?php

namespace spec\Xabbuh\XApi\Client;

use Http\Client\HttpClient;
use Http\Message\RequestFactory;
use PhpSpec\ObjectBehavior;

class XApiClientBuilderSpec extends ObjectBehavior
{
    function it_is_an_xapi_client_builder()
    {
        $this->shouldHaveType('Xabbuh\XApi\Client\XApiClientBuilderInterface');
    }

    function it_creates_an_xapi_client(HttpClient $httpClient, RequestFactory $requestFactory)
    {
        $this->setHttpClient($httpClient);
        $this->setRequestFactory($requestFactory);
        $this->setBaseUrl('http://example.com/xapi/');
        $this->build()->shouldHaveType('Xabbuh\XApi\Client\XApiClientInterface');
    }

    function its_methods_can_be_chained(HttpClient $httpClient, RequestFactory $requestFactory)
    {
        $this->setHttpClient($httpClient)->shouldReturn($this);
        $this->setRequestFactory($requestFactory)->shouldReturn($this);
        $this->setBaseUrl('http://example.com/xapi/')->shouldReturn($this);
        $this->setVersion('1.0.0')->shouldReturn($this);
        $this->setAuth('foo', 'bar')->shouldReturn($this);
        $this->setOAuthCredentials('consumer key', 'consumer secret', 'token', 'token secret')->shouldReturn($this);
    }

    function it_throws_an_exception_if_the_http_client_is_not_configured(RequestFactory $requestFactory)
    {
        $this->setRequestFactory($requestFactory);
        $this->setBaseUrl('http://example.com/xapi/');

        $this->shouldThrow('\LogicException')->during('build');
    }

    function it_throws_an_exception_if_the_request_factory_is_not_configured(HttpClient $httpClient)
    {
        $this->setHttpClient($httpClient);
        $this->setBaseUrl('http://example.com/xapi/');

        $this->shouldThrow('\LogicException')->during('build');
    }

    function it_throws_an_exception_if_the_base_uri_is_not_configured(HttpClient $httpClient, RequestFactory $requestFactory)
    {
        $this->setHttpClient($httpClient);
        $this->setRequestFactory($requestFactory);

        $this->shouldThrow('\LogicException')->during('build');
    }
}
