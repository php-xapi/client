<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\XApi\Client;

use Xabbuh\XApi\Model\Activity;
use Xabbuh\XApi\Model\Actor;
use Xabbuh\XApi\Model\Verb;

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
     * @param Actor $actor The Actor to filter by
     *
     * @return StatementsFilterInterface The statements filter
     *
     * @throws \InvalidArgumentException if the Actor is not identified
     */
    public function byActor(Actor $actor);

    /**
     * Filters by a verb.
     *
     * @param Verb $verb The Verb to filter by
     *
     * @return StatementsFilterInterface The statements filter
     */
    public function byVerb(Verb $verb);

    /**
     * Filter by an Activity.
     *
     * @param Activity $activity The Activity to filter by
     *
     * @return StatementsFilterInterface The statements filter
     */
    public function byActivity(Activity $activity);

    /**
     * Filters for Statements matching the given registration id.
     *
     * @param string $registration A registration id
     *
     * @return StatementsFilterInterface The statements filter
     */
    public function byRegistration($registration);

    /**
     * Applies the Activity filter to Sub-Statements.
     *
     * @return StatementsFilterInterface The statements filter
     */
    public function enableRelatedActivityFilter();

    /**
     * Don't apply the Activity filter to Sub-Statements.
     *
     * @return StatementsFilterInterface The statements filter
     */
    public function disableRelatedActivityFilter();

    /**
     * Applies the Agent filter to Sub-Statements.
     *
     * @return StatementsFilterInterface The statements filter
     */
    public function enableRelatedAgentFilter();

    /**
     * Don't apply the Agent filter to Sub-Statements.
     *
     * @return StatementsFilterInterface The statements filter
     */
    public function disableRelatedAgentFilter();

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
     * Specifies the format of the StatementResult being returned.
     *
     * "ids": Includes only information for the Agent, Activity and Group
     * needed to identify them.
     *
     * "exact": Agents, Groups and Activities will be returned as they were when
     * the Statements where received by the LRS.
     *
     * "canonical": For objects containing language maps, only the most appropriate
     * language will be returned. Agent objects will be returned as if the "exact"
     * format was given.
     *
     * @param string $format A valid format identifier (one of "ids", "exact"
     *                       or "canonical"
     *
     * @return StatementsFilterInterface The statements filter
     *
     * @throws \InvalidArgumentException if no valid format is given
     */
    public function format($format);

    /**
     * Query attachments for each Statement being returned.
     *
     * @return StatementsFilterInterface The statements filter
     */
    public function includeAttachments();

    /**
     * Don't query for Statement attachments (the default behavior).
     *
     * @return StatementsFilterInterface The statements filter
     */
    public function excludeAttachments();

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
