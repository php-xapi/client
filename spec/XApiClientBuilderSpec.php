<?php

namespace spec\Xabbuh\XApi\Client;

use PhpSpec\ObjectBehavior;

class XApiClientBuilderSpec extends ObjectBehavior
{
    function it_is_an_xapi_client_builder()
    {
        $this->shouldHaveType('Xabbuh\XApi\Client\XApiClientBuilderInterface');
    }

    function it_creates_an_xapi_client()
    {
        $this->build()->shouldHaveType('Xabbuh\XApi\Client\XApiClientInterface');
    }
}
