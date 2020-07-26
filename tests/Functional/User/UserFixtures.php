<?php

declare(strict_types=1);

namespace App\Tests\Functional\User;

use App\Entity\User;
use App\Enum\UserRole;
use App\Tests\Builder\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserFixtures extends Fixture
{
    public const REFERENCE_ADMIN = 'test_auth_admin';
    public const REFERENCE_USER = 'test_auth_user';
    public const REFERENCE_BLOGGER = 'test_auth_blogger';
    private const DEFAULT_PASSWORD = 'password';

    private $passwordFactory;

    public function __construct(EncoderFactoryInterface $passwordFactory)
    {
        $this->passwordFactory = $passwordFactory;
    }

    public function load(ObjectManager $manager)
    {
        $passwordEncoder = $this->passwordFactory->getEncoder(User::class);
        $hashedPassword = $passwordEncoder->encodePassword(self::DEFAULT_PASSWORD, null);

        $admin = (new UserBuilder())
            ->viaEmail(self::adminCredentials()['email'], $hashedPassword)
            ->withRole(UserRole::ROLE_ADMIN())
            ->build()
        ;

        $manager->persist($admin);
        $this->setReference(self::REFERENCE_ADMIN, $admin);

        $user = (new UserBuilder())
            ->viaEmail(self::userCredentials()['email'], $hashedPassword)
            ->withRole(UserRole::ROLE_USER())
            ->build()
        ;

        $manager->persist($user);
        $this->setReference(self::REFERENCE_USER, $user);

        $blogger = (new UserBuilder())
            ->viaEmail(self::bloggerCredentials()['email'], $hashedPassword)
            ->withRole(UserRole::ROLE_BLOGGER())
            ->build()
        ;

        $manager->persist($blogger);
        $this->setReference(self::REFERENCE_BLOGGER, $blogger);

        $manager->flush();
    }

    public static function adminCredentials(): array
    {
        return [
            'email' => 'auth-admin@app.test',
            'password' => 'password',
        ];
    }

    public static function userCredentials(): array
    {
        return [
            'email' => 'auth-user@app.test',
            'password' => 'password',
        ];
    }

    public static function bloggerCredentials(): array
    {
        return [
            'email' => 'auth-blogger@app.test',
            'password' => 'password',
        ];
    }
}