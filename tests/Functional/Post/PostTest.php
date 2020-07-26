<?php

declare(strict_types=1);

namespace App\Tests\Functional\Post;

use App\Entity\Post;
use App\Tests\Functional\BaseApiTestCase;
use App\Tests\Functional\User\UserFixtures;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostTest extends BaseApiTestCase
{
    private const URI = '/api/posts';

    public function testGetCollection(): void
    {
        $response = $this->client->request(Request::METHOD_GET, self::URI);

        $this->assertResponseIsSuccessful();
        $this->assertCount(10, $response->toArray()['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Post::class);
    }

    public function testCreatePost(): void
    {
        $this->authenticateClient(UserFixtures::bloggerCredentials());

        $response = $this->client->request(Request::METHOD_POST, self::URI, [
            'json' => [
                'title' => 'Super fancy post title',
                'content' => 'Post content'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertMatchesResourceCollectionJsonSchema(Post::class);
        $this->assertMatchesPattern([
            '@context' => '/api/contexts/Post',
            '@id' => '@string@',
            '@type' => 'Post',
            'title' => '@string@',
            'content' => '@string@',
            'createdAt' => '@string@.isDateTime()'
        ], $response->toArray());
    }

    public function testNotVerifiedUserCannotCreatePost(): void
    {
        $this->authenticateClient(UserFixtures::userCredentials());

        $this->client->request(Request::METHOD_POST, self::URI, [
            'json' => [
                'title' => 'Super fancy post title',
                'content' => 'Post content'
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testGet(): void
    {
        $iri = $this->findIriBy(Post::class, [
            'title' => sprintf(PostFixtures::POST_TITLE, 1)
        ]);

        $response = $this->client->request(Request::METHOD_GET, $iri);

        $this->assertResponseIsSuccessful();
        $this->assertMatchesPattern([
            '@context' => '/api/contexts/Post',
            '@id' => '@string@',
            '@type' => 'Post',
            'title' => '@string@',
            'content' => '@string@',
            'createdAt' => '@string@.isDateTime()'
        ], $response->toArray());
    }

    public function testAuthorCanUpdateHisPost(): void
    {
        $iri = $this->findIriBy(Post::class, [
            'title' => sprintf(PostFixtures::POST_TITLE, 1)
        ]);
        $this->authenticateClient(UserFixtures::bloggerCredentials());

        $this->client->request(Request::METHOD_PUT, $iri, [
            'json' => [
                'title' => 'Changed title',
                'content' => 'Changed content'
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'title' => 'Changed title',
            'content' => 'Changed content'
        ]);
    }

    public function testNotAuthorCannotUpdatePost(): void
    {
        $iri = $this->findIriBy(Post::class, [
            'title' => sprintf(PostFixtures::POST_TITLE, 1)
        ]);
        $this->authenticateClient(UserFixtures::userCredentials());

        $this->client->request(Request::METHOD_PUT, $iri, [
            'json' => [
                'title' => 'Changed title',
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAuthorCanDeletePost(): void
    {
        $iri = $this->findIriBy(Post::class, [
            'title' => sprintf(PostFixtures::POST_TITLE, 1)
        ]);
        $this->authenticateClient(UserFixtures::bloggerCredentials());

        $this->client->request(Request::METHOD_DELETE, $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testNotAuthorCannotDeletePost(): void
    {
        $iri = $this->findIriBy(Post::class, [
            'title' => sprintf(PostFixtures::POST_TITLE, 1)
        ]);
        $this->authenticateClient(UserFixtures::userCredentials());

        $this->client->request(Request::METHOD_DELETE, $iri);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}