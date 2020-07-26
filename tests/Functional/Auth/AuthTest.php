<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth;

use App\Tests\Functional\BaseApiTestCase;
use App\Tests\Functional\User\UserFixtures;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthTest extends BaseApiTestCase
{
    public const AUTH_URI = '/authentication_token';

    public function testInvalidMethod(): void
    {
        $this->client->request(Request::METHOD_GET, self::AUTH_URI);

        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testSuccessLogin(): void
    {
        $response = $this->client->request(Request::METHOD_POST, self::AUTH_URI, [
            'json' => UserFixtures::userCredentials()
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $response->toArray());
    }

    public function testLoginFailure(): void
    {
        $this->client->request(Request::METHOD_POST, self::AUTH_URI, ['json' => [
            'email' => 'example@example.com',
            'password' => 'secret'
        ]]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertJsonContains(['code' => 401, 'message' => 'Invalid credentials.']);
    }
}