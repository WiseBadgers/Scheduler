<?php

declare(strict_types=1);

namespace App\Test;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class CustomApiTestCase extends ApiTestCase
{
    protected function createUser(string $email, string $password, array $roles): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setFirstName(substr($email, 0, strpos($email, '@')));
        $user->setLastName('test');
        $user->setRoles($roles);
        $hashedPassword = $this->getPasswordHasher()->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    protected function logIn(Client $client, string $email, string $password): void
    {
        $client->request('POST', 'login', [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => [
                    'email' => $email,
                    'password' => 'test',
                ],
            ]
        );
        $this->assertResponseStatusCodeSame(204);
    }

    protected function createUserAndLogIn(Client $client, string $email, string $password, array $roles): User
    {
        $user = $this->createUser($email, $password, $roles);
        $this->logIn($client, $email, 'test');

        return $user;
    }

    protected function getPasswordHasher(): UserPasswordHasherInterface
    {
        $passwordHasherFactory = new PasswordHasherFactory([
           PasswordAuthenticatedUserInterface::class => ['algorithm' => 'auto'],
        ]);

        return new UserPasswordHasher($passwordHasherFactory);
    }

    protected function getEntityManager(): ObjectManager
    {
        return self::getContainer()->get('doctrine')->getManager();
    }
}
