<?php

declare(strict_types=1);

namespace App\Tests;

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
        $this->logIn($client, 'test@test.com', 'test');
    }

    public function testPatchNote(): void
    {
        $client = self::createClient();
        $user = $this->createUserAndLogIn($client, 'test@test.com', 'test', []);

        $client->request('PATCH', '/api/users/'.$user->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['firstName' => 'Predator'],
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['firstName' => 'Predator']);
    }
}
