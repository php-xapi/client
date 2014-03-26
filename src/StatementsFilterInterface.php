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
interface StatementsFilterInterface
{
    /**
     * Filters by an Agent or an identified Group.
     *
     * @param ActorInterface $actor The Actor to filter by
     *
     * @return StatementsFilterInterface The statements filter
     *
     * @throws \InvalidArgumentException if the Actor is not identified
     */
    public function byActor(ActorInterface $actor);

    /**
     * Filters by a verb.
     *
     * @param VerbInterface $verb The Verb to filter by
     *
     * @return StatementsFilterInterface The statements filter
     */
    public function byVerb(VerbInterface $verb);

    /**
     * Filter by an Activity.
     *
     * @param ActivityInterface $activity The Activity to filter by
     *
     * @return StatementsFilterInterface The statements filter
     */
    public function byActivity(ActivityInterface $activity);

    /**
     * Filters for Statements stored since the specified timestamp (exclusive).
     *
     * @param \DateTime $timestamp The timestamp
     *
     * @return StatementsFilterInterface The statements filter
     */
    public function since(\DateTime $timestamp);

    /**
     * Filters for Statements stored at or before the specified timestamp.
     *
     * @param \DateTime $timestamp The timestamp as a unix timestamp
     *
     * @return StatementsFilterInterface The statements filter
     */
    public function until(\DateTime $timestamp);

    /**
     * Sets the maximum number of Statements to return. The server side sets
     * the maximum number of results when this value is not set or when it is 0.
     *
     * @param int $limit Maximum number of Statements to return
     *
     * @return StatementsFilterInterface The statements filter
     *
     * @throws \InvalidArgumentException if the limit is not a non-negative
     *                                   integer
     */
    public function limit($limit);

    /**
     * Return statements in ascending order of stored time.
     *
     * @return StatementsFilterInterface The statements filter
     */
    public function ascending();

    /**
     *Return statements in descending order of stored time (the default behavior).
     *
     * @return StatementsFilterInterface The statements filter
     */
    public function descending();

    /**
     * Returns the generated filter.
     *
     * @return array The filter
     */
    public function getFilter();
}
