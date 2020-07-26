<?php

declare(strict_types=1);

namespace App\Tests\Functional\VerificationRequest;

use App\Entity\VerificationRequest;
use App\Enum\VerificationRequestStatus;
use App\Tests\Functional\BaseApiTestCase;
use App\Tests\Functional\User\UserFixtures;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificationRequestTest extends BaseApiTestCase
{
    private const URI = '/api/verification_requests';

    public function testUserCannotGetCollection(): void
    {
        $this->authenticateClient(UserFixtures::userCredentials());

        $this->client->request(Request::METHOD_GET, self::URI);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAdminCanGetCollection(): void
    {
        $this->authenticateClient(UserFixtures::adminCredentials());

        $response = $this->client->request(Request::METHOD_GET, self::URI);

        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $response->toArray()['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(VerificationRequest::class);
    }

    public function testGet(): void
    {
        $iri = $this->findIriBy(VerificationRequest::class, [
            'status' => VerificationRequestStatus::VERIFICATION_REQUESTED
        ]);
        $this->authenticateClient(UserFixtures::userCredentials());

        $response = $this->client->request(Request::METHOD_GET, $iri);

        $this->assertResponseIsSuccessful();
        $this->assertMatchesPattern([
            '@context' => '/api/contexts/VerificationRequest',
            '@id' => '@string@',
            '@type' => 'VerificationRequest',
            'message' => '@string@',
            'status' => '@string@.contains("verification_requested")',
            'createdAt' => '@string@.isDateTime()'
        ], $response->toArray());
    }

    public function testNotAuthorCannotAccessVerificationRequest(): void
    {
        $iri = $this->findIriBy(VerificationRequest::class, [
            'status' => VerificationRequestStatus::APPROVED
        ]);
        $this->authenticateClient(UserFixtures::userCredentials());

        $this->client->request(Request::METHOD_GET, $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAuthorCanUpdate(): void
    {
        $iri = $this->findIriBy(VerificationRequest::class, [
            'status' => VerificationRequestStatus::VERIFICATION_REQUESTED
        ]);
        $this->authenticateClient(UserFixtures::userCredentials());

        $this->client->request(Request::METHOD_PUT, $iri, [
            'json' => ['message' => 'Some new fancy message']
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'message' => 'Some new fancy message'
        ]);
    }

    public function testAuthorCannotUpdateReviewed(): void
    {
        $iri = $this->findIriBy(VerificationRequest::class, [
            'status' => VerificationRequestStatus::APPROVED
        ]);
        $this->authenticateClient(UserFixtures::bloggerCredentials());

        $this->client->request(Request::METHOD_PUT, $iri, [
            'json' => ['message' => 'Some new fancy message']
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testApprove(): void
    {
        $iri = $this->findIriBy(VerificationRequest::class, [
            'status' => VerificationRequestStatus::VERIFICATION_REQUESTED
        ]);
        $this->authenticateClient(UserFixtures::adminCredentials());

        $response = $this->client->request(Request::METHOD_PUT, sprintf('%s/approve', $iri), [
            'json' => []
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertMatchesPattern([
            '@context' => '/api/contexts/VerificationRequest',
            '@id' => '@string@',
            '@type' => 'VerificationRequest',
            'message' => '@string@',
            'status' => '@string@.contains("approved")',
            'createdAt' => '@string@.isDateTime()'
        ], $response->toArray());
    }

    public function testDecline(): void
    {
        $iri = $this->findIriBy(VerificationRequest::class, [
            'status' => VerificationRequestStatus::VERIFICATION_REQUESTED
        ]);
        $this->authenticateClient(UserFixtures::adminCredentials());

        $response = $this->client->request(Request::METHOD_PUT, sprintf('%s/decline', $iri), [
            'json' => [
                'rejectReason' => 'Some reason'
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertMatchesPattern([
            '@context' => '/api/contexts/VerificationRequest',
            '@id' => '@string@',
            '@type' => 'VerificationRequest',
            'message' => '@string@',
            'status' => '@string@.contains("declined")',
            'rejectReason' => '@string@.contains("Some reason")',
            'createdAt' => '@string@.isDateTime()'
        ], $response->toArray());
    }

    public function testAuthorCanDeleteVerificationRequest(): void
    {
        $iri = $this->findIriBy(VerificationRequest::class, [
            'status' => VerificationRequestStatus::VERIFICATION_REQUESTED
        ]);
        $this->authenticateClient(UserFixtures::userCredentials());

        $this->client->request(Request::METHOD_DELETE, $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testNotAuthorCannotDeleteVerificationRequest(): void
    {
        $iri = $this->findIriBy(VerificationRequest::class, [
            'status' => VerificationRequestStatus::VERIFICATION_REQUESTED
        ]);
        $this->authenticateClient(UserFixtures::bloggerCredentials());

        $this->client->request(Request::METHOD_DELETE, $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}