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

use Xabbuh\XApi\Common\Model\ActivityInterface;
use Xabbuh\XApi\Common\Model\ActorInterface;
use Xabbuh\XApi\Common\Model\VerbInterface;

/**
 * Filter to apply on GET requests to the statements API.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class StatementsFilter implements StatementsFilterInterface
{
    /**
     * @var array The generated filter
     */
    private $filter = array();

    /**
     * {@inheritDoc}
     */
    public function byActor(ActorInterface $actor)
    {
        if (null === $actor->getInverseFunctionalIdentifier()) {
            throw new \InvalidArgumentException('Actor must be identified');
        }

        $this->filter['agent'] = $actor;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function byVerb(VerbInterface $verb)
    {
        $this->filter['verb'] = $verb->getId();

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function byActivity(ActivityInterface $activity)
    {
        $this->filter['activity'] = $activity->getId();

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function byRegistration($registration)
    {
        $this->filter['registration'] = $registration;
    }

    /**
     * {@inheritDoc}
     */
    public function enableRelatedActivityFilter()
    {
        $this->filter['related_activities'] = true;
    }

    /**
     * {@inheritDoc}
     */
    public function disableRelatedActivityFilter()
    {
        $this->filter['related_activities'] = false;
    }

    /**
     * {@inheritDoc}
     */
    public function enableRelatedAgentFilter()
    {
        $this->filter['related_agents'] = true;
    }

    /**
     * {@inheritDoc}
     */
    public function disableRelatedAgentFilter()
    {
        $this->filter['related_agents'] = false;
    }

    /**
     * {@inheritDoc}
     */
    public function since(\DateTime $timestamp)
    {
        $this->filter['since'] = $timestamp->format('c');

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function until(\DateTime $timestamp)
    {
        $this->filter['until'] = $timestamp->format('c');

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function limit($limit)
    {
        if ($limit < 0) {
            throw new \InvalidArgumentException('Limit must be a non-negative integer');
        }

        $this->filter['limit'] = $limit;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function format($format)
    {
        if (!in_array($format, array('ids', 'exact', 'canonical'))) {
            throw new \InvalidArgumentException('Unknown format '.$format.' given');
        }

        $this->filter['format'] = $format;
    }

    /**
     * {@inheritDoc}
     */
    public function includeAttachments()
    {
        $this->filter['attachments'] = true;
    }

    /**
     * {@inheritDoc}
     */
    public function excludeAttachments()
    {
        $this->filter['attachments'] = false;
    }

    /**
     * {@inheritDoc}
     */
    public function ascending()
    {
        $this->filter['ascending'] = 'True';

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function descending()
    {
        $this->filter['ascending'] = 'False';

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilter()
    {
        return $this->filter;
    }
}
