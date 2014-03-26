<?php

/*
 * This file is part of the XabbuhXApiClient package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\XApi\Client\Tests;

use Xabbuh\XApi\Client\StatementsFilter;
use Xabbuh\XApi\Common\Model\Activity;
use Xabbuh\XApi\Common\Model\Agent;
use Xabbuh\XApi\Common\Model\Group;
use Xabbuh\XApi\Common\Model\Verb;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class StatementsFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StatementsFilter
     */
    private $statementsFilter;

    protected function setUp()
    {
        $this->statementsFilter = new StatementsFilter();
    }

    public function testByActor()
    {
        $agent = new Agent();
        $agent->setMbox('alice@example.com');
        $this->statementsFilter->byActor($agent);
        $filter = $this->statementsFilter->getFilter();

        $this->assertEquals($agent, $filter['agent']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testByActorWithNotIdentifiedGroup()
    {
        $this->statementsFilter->byActor(new Group());
    }

    public function testByVerb()
    {
        $verb = new Verb();
        $verb->setId('http://adlnet.gov/expapi/verbs/attended');
        $this->statementsFilter->byVerb($verb);
        $filter = $this->statementsFilter->getFilter();

        $this->assertEquals(
            'http://adlnet.gov/expapi/verbs/attended',
            $filter['verb']
        );
    }

    public function testByActivity()
    {
        $activity = new Activity();
        $activity->setId('8f87ccde-bb56-4c2e-ab83-44982ef22df0');
        $this->statementsFilter->byActivity($activity);
        $filter = $this->statementsFilter->getFilter();

        $this->assertEquals(
            '8f87ccde-bb56-4c2e-ab83-44982ef22df0',
            $filter['activity']
        );
    }

    public function testSince()
    {
        $timestamp = \DateTime::createFromFormat(\DateTime::ISO8601, '2013-05-18T05:32:34Z');
        $this->statementsFilter->since($timestamp);
        $filter = $this->statementsFilter->getFilter();

        $this->assertEquals(
            '2013-05-18T05:32:34+00:00',
            $filter['since']
        );
    }

    public function testUntil()
    {
        $timestamp = \DateTime::createFromFormat(\DateTime::ISO8601, '2013-05-18T05:32:34Z');
        $this->statementsFilter->until($timestamp);
        $filter = $this->statementsFilter->getFilter();

        $this->assertEquals(
            '2013-05-18T05:32:34+00:00',
            $filter['until']
        );
    }

    public function testLimit()
    {
        $this->statementsFilter->limit(10);
        $filter = $this->statementsFilter->getFilter();

        $this->assertEquals(10, $filter['limit']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLimitWithNegativeArgument()
    {
        $this->statementsFilter->limit(-1);
    }

    public function testAscending()
    {
        $this->statementsFilter->ascending();
        $filter = $this->statementsFilter->getFilter();

        $this->assertEquals('True', $filter['ascending']);
    }

    public function testDescending()
    {
        $this->statementsFilter->descending();
        $filter = $this->statementsFilter->getFilter();

        $this->assertEquals('False', $filter['ascending']);
    }
}
