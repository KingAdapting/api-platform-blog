<?php

declare(strict_types=1);

namespace App\Tests\Functional\User;

use App\Entity\User;
use App\Tests\Functional\BaseApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserTest extends BaseApiTestCase
{
    private const URI = '/api/users';

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
        $this->assertCount(3, $response->toArray()['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(User::class);
    }

    public function testCreateUser(): void
    {
        $response = $this->client->request(Request::METHOD_POST, sprintf(self::URI, ''), [
            'json' => [
                'email' => 'example@example.com',
                'password' => 'password',
                'firstName' => 'Test',
                'lastName' => 'Test'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertMatchesResourceCollectionJsonSchema(User::class);
        $this->assertMatchesPattern([
            '@context' => '/api/contexts/User',
            '@id' => '@string@',
            '@type' => 'User',
            'email' => '@string@.isEmail()',
            'roles' => '@array@',
            'firstName' => '@string@',
            'lastName' => '@string@',
            'createdAt' => '@string@.isDateTime()'
        ], $response->toArray());
    }

    public function testCreateInvalidUser(): void
    {
        $response = $this->client->request(Request::METHOD_POST, sprintf(self::URI, ''), [
            'json' => ['email' => 'example@example.com']
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertMatchesPattern([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => '@string@',
            'violations' => '@array@.count(3)',
        ], $response->toArray(false));
    }

    public function testUpdateUser(): void
    {
        $this->authenticateClient($credentials = UserFixtures::userCredentials());
        $iri = $this->findIriBy(User::class, ['email' => $credentials['email']]);

        $response = $this->client->request(Request::METHOD_PUT, $iri, [
            'json' => [
                'password' => 'newPassword',
                'firstName' => 'Mark',
                'lastName' => 'Hunt'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertMatchesPattern([
            '@context' => '/api/contexts/User',
            '@id' => '@string@',
            '@type' => 'User',
            'email' => '@string@.isEmail()',
            'firstName' => 'Mark',
            'lastName' => 'Hunt',
            'roles' => '@array@',
            'createdAt' => '@string@.isDateTime()'
        ], $response->toArray());
    }

    public function testUserCannotDeleteAccount(): void
    {
        $this->authenticateClient($credentials = UserFixtures::userCredentials());
        $iri = $this->findIriBy(User::class, ['email' => $credentials['email']]);

        $this->client->request(Request::METHOD_DELETE, $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAdminCanDeleteAccount(): void
    {
        $this->authenticateClient($credentials = UserFixtures::adminCredentials());
        $iri = $this->findIriBy(User::class, ['email' => $credentials['email']]);

        $this->client->request(Request::METHOD_DELETE, $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}