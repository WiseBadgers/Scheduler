<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\User;
use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class UserTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateUser(): void
    {
        $client = self::createClient();
        $this->createUserAndLogIn($client, 'admin@test.com', 'test', ['ROLE_ADMIN']);

        $client->request('POST', '/api/users', [
            'json' => [
                'firstName' => 'test',
                'lastName' => 'test',
                'email' => 'test@test.com',
                'roles' => ['ROLE_STUDENT'],
                'password' => 'test',
            ],
        ]);
        $this->assertResponseStatusCodeSame(201);
    }

    public function testGetUser(): void
    {
        $client = self::createClient();
        $user1 = $this->createUserAndLogIn($client, 'test@test.com', 'test', ['ROLE_TEACHER']);
        $user1->setPhoneNumber('234234234');
        $em = $this->getEntityManager();
        $em->flush();

        $client->request('GET', '/api/users/'.$user1->getId());
        $data = $client->getResponse()->toArray();
        $this->assertArrayHasKey('phoneNumber', $data);

        // refreshes a user and gives user ROLE_STUDENT
        $user1 = $em->getRepository(User::class)->find($user1->getId());
        $user1->setRoles(['ROLE_STUDENT']);
        $em->flush();
        $this->logIn($client, 'test@test.com', 'test');

        $client->request('GET', '/api/users/'.$user1->getId());
        $data = $client->getResponse()->toArray();
        $this->assertArrayHasKey('phoneNumber', $data);

        $user2 = $this->createUser('user2@test.com', 'test', ['ROLE_STUDENT']);
        $user2->setPhoneNumber('123123123');
        $em = $this->getEntityManager();
        $em->flush();
        $this->logIn($client, 'test@test.com', 'test');

        $client->request('GET', '/api/users/'.$user2->getId());
        $data = $client->getResponse()->toArray();
        $this->assertArrayNotHasKey('phoneNumber', $data);
    }

    public function testPatchUser(): void
    {
        $client = self::createClient();

        $user1 = $this->createUserAndLogIn($client, 'test@test.com', 'test', []);
        $client->request('PATCH', '/api/users/'.$user1->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'firstName' => 'Predator',
                'roles' => ['ROLE_ADMIN'],
            ],
        ]);
        $this->assertResponseStatusCodeSame(403);

        $this->createUserAndLogIn($client, 'admin@test.com', 'test', ['ROLE_ADMIN']);
        $client->request('PATCH', '/api/users/'.$user1->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'firstName' => 'Predator',
                'roles' => ['ROLE_ADMIN'],
            ],
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['firstName' => 'Predator']);

        $em = $this->getEntityManager();
        /** @var User $user1 */
        $user1 = $em->getRepository(User::class)->find($user1->getId());
        // check if $user1 has ROLE_ADMIN
    }
}
