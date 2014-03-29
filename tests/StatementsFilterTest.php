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

    public function testByRegistration()
    {
        $this->statementsFilter->byRegistration('foo');
        $filter = $this->statementsFilter->getFilter();

        $this->assertEquals($filter['registration'], 'foo');
    }

    public function testEnableRelatedActivityFilter()
    {
        $this->statementsFilter->enableRelatedActivityFilter();
        $filter = $this->statementsFilter->getFilter();

        $this->assertTrue($filter['related_activities']);
    }

    public function testDisableRelatedActivityFilter()
    {
        $this->statementsFilter->disableRelatedActivityFilter();
        $filter = $this->statementsFilter->getFilter();

        $this->assertFalse($filter['related_activities']);
    }

    public function testEnableRelatedAgentFilter()
    {
        $this->statementsFilter->enableRelatedAgentFilter();
        $filter = $this->statementsFilter->getFilter();

        $this->assertTrue($filter['related_agents']);
    }

    public function testDisableRelatedAgentFilter()
    {
        $this->statementsFilter->disableRelatedAgentFilter();
        $filter = $this->statementsFilter->getFilter();

        $this->assertFalse($filter['related_agents']);
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

    public function testIdsFormat()
    {
        $this->statementsFilter->format('ids');
        $filter = $this->statementsFilter->getFilter();

        $this->assertEquals('ids', $filter['format']);
    }

    public function testExactFormat()
    {
        $this->statementsFilter->format('exact');
        $filter = $this->statementsFilter->getFilter();

        $this->assertEquals('exact', $filter['format']);
    }

    public function testCanonicalFormat()
    {
        $this->statementsFilter->format('canonical');
        $filter = $this->statementsFilter->getFilter();

        $this->assertEquals('canonical', $filter['format']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidFormat()
    {
        $this->statementsFilter->format('minimal');
    }

    public function testIncludeAttachments()
    {
        $this->statementsFilter->includeAttachments();
        $filter = $this->statementsFilter->getFilter();

        $this->assertTrue($filter['attachments']);
    }

    public function testExcludeAttachments()
    {
        $this->statementsFilter->excludeAttachments();
        $filter = $this->statementsFilter->getFilter();

        $this->assertFalse($filter['attachments']);
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
