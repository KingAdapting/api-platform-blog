<?php

declare(strict_types=1);

namespace App\Tests\Functional\Post;

use App\DataFixtures\AbstractFixture;
use App\Entity\Post;
use App\Tests\Functional\User\UserFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PostFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public const POST_TITLE = 'Blog Post № %s';
    private const COUNT = 10;

    protected function loadData(ObjectManager $manager): void
    {
        $this->createMany(self::COUNT, 'posts', function ($i) {
            return (new Post())
                ->setTitle(sprintf(self::POST_TITLE, $i))
                ->setContent($this->faker->realText(100))
                ->setAuthor($this->getReference(UserFixtures::REFERENCE_BLOGGER))
            ;
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class
        ];
    }
}