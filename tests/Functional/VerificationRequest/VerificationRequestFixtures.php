<?php

declare(strict_types=1);

namespace App\Tests\Functional\VerificationRequest;

use App\DataFixtures\AbstractFixture;
use App\Entity\VerificationRequest;
use App\Enum\VerificationRequestStatus;
use App\Tests\Functional\User\UserFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class VerificationRequestFixtures extends AbstractFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        $data = $this->getData();

        $this->createMany(count($data), 'verification_requests', function ($i) use ($data) {
            return (new VerificationRequest())
                ->setMessage($this->faker->realText(15))
                ->setAuthor($this->getReference($data[$i]['authorReference']))
                ->setStatus($data[$i]['status'])
                ->setIdentityDocumentFileName($this->faker->sha1)
            ;
        });

        $manager->flush();
    }

    private function getData(): array
    {
        return [
            [
                'authorReference' => UserFixtures::REFERENCE_BLOGGER,
                'status' => VerificationRequestStatus::APPROVED
            ],
            [
                'authorReference' => UserFixtures::REFERENCE_USER,
                'status' => VerificationRequestStatus::VERIFICATION_REQUESTED
            ],
        ];
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class
        ];
    }
}