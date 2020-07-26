<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Functional\Auth\AuthTest;
use Coduo\PHPMatcher\PHPUnit\PHPMatcherAssertions;
use Symfony\Component\HttpFoundation\Request;

class BaseApiTestCase extends ApiTestCase
{
    use PHPMatcherAssertions;

    private $entityManager;
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->client->disableReboot();

        $this->entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->entityManager->getConnection()->beginTransaction();
        $this->entityManager->getConnection()->setAutoCommit(false);
    }

    public function tearDown(): void
    {
        $this->entityManager->getConnection()->rollback();
        $this->entityManager->close();

        parent::tearDown();
    }

    protected function authenticateClient(array $credentials): void
    {
        $response = $this->client->request(Request::METHOD_POST, AuthTest::AUTH_URI, [
            'json' => $credentials
        ]);

        $this->client->setDefaultOptions(['auth_bearer' => $response->toArray()['token']]);
    }
}