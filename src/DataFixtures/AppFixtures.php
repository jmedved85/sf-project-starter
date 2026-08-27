<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getUsers() as $userData) {
            $user = (new User())
                ->setEmail($userData['email'])
                ->setUserName($userData['userName'])
                ->setRoles($userData['roles'])
                ->setFirstName($userData['firstName'])
                ->setLastName($userData['lastName'])
                ->setActive($userData['active']);

            $user->setPassword($this->passwordHasher->hashPassword($user, $userData['plainPassword']));

            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * @return array<int, array{email: string, userName: string, plainPassword: string, roles: list<string>, firstName: string, lastName: string, active: bool}>
     */
    private function getUsers(): array
    {
        return [
            [
                'email' => 'admin@net.com',
                'userName' => 'admin',
                'plainPassword' => 'admin1234',
                'roles' => ['ROLE_ADMIN'],
                'firstName' => 'Admin',
                'lastName' => 'User',
                'active' => true,
            ],
            [
                'email' => 'user@net.com',
                'userName' => 'user',
                'plainPassword' => 'user1234',
                'roles' => ['ROLE_USER'],
                'firstName' => 'Joe',
                'lastName' => 'Doe',
                'active' => true,
            ],
        ];
    }
}
