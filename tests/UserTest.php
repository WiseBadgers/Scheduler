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

        $client->request('POST', '/api/users', [
            'json' => [
                'firstName' => 'test',
                'lastName' => 'test',
                'email' => 'test@test.com',
                'password' => 'test',
            ],
        ]);
        $this->assertResponseStatusCodeSame(201);
    }

    public function testGetUser(): void
    {
        $client = self::createClient();
        $user = $this->createUserAndLogIn($client, 'test@test.com', 'test', []);

        $user->setPhoneNumber('234234234');
        $em = $this->getEntityManager();
        $em->flush();

        $client->request('GET', '/api/users/'.$user->getId());
        $this->assertJsonContains(['email' => 'test@test.com']);
        $data = $client->getResponse()->toArray();
        $this->assertArrayNotHasKey('phoneNumber', $data);

        // refreshes a user and elevates user to ROLE_ADMIN
        $user = $em->getRepository(User::class)->find($user->getId());
        $user->setRoles(['ROLE_TEACHER']);
        $em->flush();
        $this->logIn($client, 'test@test.com', 'test');

        $client->request('GET', '/api/users/'.$user->getId());
        $data = $client->getResponse()->toArray();
        $this->assertArrayHasKey('phoneNumber', $data);
    }

    public function testPatchUser(): void
    {
        $client = self::createClient();
        $user = $this->createUserAndLogIn($client, 'test@test.com', 'test', []);

        $client->request('PATCH', '/api/users/'.$user->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => [
                'firstName' => 'Predator',
                'roles' => ['ROLE_ADMIN'],
            ],
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['firstName' => 'Predator']);

        $em = $this->getEntityManager();
        /** @var User $user */
        $user = $em->getRepository(User::class)->find($user->getId());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());


    }
}
