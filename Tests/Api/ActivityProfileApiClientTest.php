<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xabbuh\XApi\Client\Tests\Api;

use Xabbuh\XApi\Client\Api\ActivityProfileApiClient;
use Xabbuh\XApi\Model\Activity;
use Xabbuh\XApi\Model\ActivityProfile;
use Xabbuh\XApi\Model\ActivityProfileDocument;
use Xabbuh\XApi\Common\Serializer\DocumentSerializer;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class ActivityProfileApiClientTest extends ApiClientTest
{
    /**
     * @var ActivityProfileApiClient
     */
    private $client;

    protected function setUp()
    {
        parent::setUp();
        $this->client = new ActivityProfileApiClient(
            $this->requestHandler,
            '1.0.1',
            new DocumentSerializer($this->serializer)
        );
    }

    public function testCreateOrUpdateDocument()
    {
        $document = $this->createActivityProfileDocument();

        $this->validateStoreApiCall(
            'post',
            'activities/profile',
            array(
                'activityId' => 'activity-id',
                'profileId' => 'profile-id',
            ),
            204,
            '',
            $document
        );

        $this->client->createOrUpdateDocument($document);
    }

    public function testCreateOrReplaceDocument()
    {
        $document = $this->createActivityProfileDocument();

        $this->validateStoreApiCall(
            'put',
            'activities/profile',
            array(
                'activityId' => 'activity-id',
                'profileId' => 'profile-id',
            ),
            204,
            '',
            $document
        );

        $this->client->createOrReplaceDocument($document);
    }

    public function testDeleteDocument()
    {
        $activityProfile = $this->createActivityProfile();

        $this->validateDeleteDocumentCall('activities/profile', array(
            'activityId' => 'activity-id',
            'profileId' => 'profile-id',
        ));

        $this->client->deleteDocument($activityProfile);
    }

    public function testGetDocument()
    {
        $activityProfile = $this->createActivityProfile();
        $document = new ActivityProfileDocument();
        $document['x'] = 'foo';

        $this->validateRetrieveApiCall(
            'get',
            'activities/profile',
            array(
                'activityId' => 'activity-id',
                'profileId' => 'profile-id',
            ),
            200,
            'ActivityProfileDocument',
            $document
        );

        $document = $this->client->getDocument($activityProfile);

        $this->assertInstanceOf('Xabbuh\XApi\Model\ActivityProfileDocument', $document);
        $this->assertEquals($activityProfile, $document->getActivityProfile());
    }

    private function createActivityProfile()
    {
        $activity = new Activity();
        $activity->setId('activity-id');
        $activityProfile = new ActivityProfile();
        $activityProfile->setActivity($activity);
        $activityProfile->setProfileId('profile-id');

        return $activityProfile;
    }

    private function createActivityProfileDocument()
    {
        $activityProfile = $this->createActivityProfile();
        $document = new ActivityProfileDocument();
        $document->setActivityProfile($activityProfile);

        return $document;
    }
}
