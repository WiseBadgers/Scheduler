<?php

declare(strict_types=1);

namespace App\Tests;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class SchoolClassTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testPostClass(): void
    {
        $client = self::createClient();

        // tests if unauthorized user gets Unauthorized
        $client->request('POST', '/api/classes', [
            'json' => ['name' => '3A'],
        ]);
        $this->assertResponseStatusCodeSame(401);

        // tests if user without ROLE_ADMIN gets Access Denied
        $this->createUserAndLogIn($client, 'not-admin@test.com', 'test', ['ROLE_TEACHER']);
        $client->request('POST', '/api/classes', [
            'json' => ['name' => '3A'],
        ]);
        $this->assertResponseStatusCodeSame(403);

        // tests if user with ROLE_ADMIN gets Success
        $this->createUserAndLogIn($client, 'admin@test.com', 'test', ['ROLE_ADMIN']);
        $client->request('POST', '/api/classes', [
            'json' => ['name' => '3A'],
        ]);
        $this->assertResponseIsSuccessful();

        // test if name that contains more than 2 chars can be added
        $client->request('POST', '/api/classes', [
            'json' => ['name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'],
        ]);
        $this->assertResponseStatusCodeSame(422);
    }

    public function testGetclass(): void
    {
        $client = self::createClient();
        $class = $this->createClass('2B');

        // tests if anonymous user gets Unauthorized
        $client->request('GET', '/api/classes/'.$class->getId());
        $this->assertResponseStatusCodeSame(401);

        // tests if logged-in user gets Success
        $this->createUserAndLogIn($client, 'test@test.com', 'test', ['ROLE_TEACHER']);
        $client->request('GET', '/api/classes/'.$class->getId());
        $this->assertResponseIsSuccessful();
    }

    public function testDeleteClass(): void
    {
        $client = $this->createClient();
        $class = $this->createclass('2B');

        // tests if anonymous user gets Unauthorized
        $client->request('DELETE', '/api/classes/'.$class->getId());
        $this->assertResponseStatusCodeSame(401);

        // tests if user without ROLE_ADMIN gets Access Denied
        $this->createUserAndLogIn($client, 'test@test.com', 'test', ['ROLE_TEACHER']);
        $client->request('DELETE', '/api/classes/'.$class->getId());
        $this->assertResponseStatusCodeSame(403);

        // tests if user with ROLE_ADMIN gets Success
        $this->createUserAndLogIn($client, 'admin@test.com', 'test', ['ROLE_ADMIN']);
        $client->request('DELETE', '/api/classes/'.$class->getId());
        $this->assertResponseIsSuccessful();
    }

    public function testPatchClass(): void
    {
        $client = $this->createClient();
        $class = $this->createClass('2B');

        // tests if anonymous user gets Unauthorized
        $client->request('PATCH', '/api/classes/'.$class->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['name' => '2C'],
        ]);
        $this->assertResponseStatusCodeSame(401);

        // tests if user without ROLE_ADMIN gets Access Denied
        $this->createUserAndLogIn($client, 'test@test.com', 'test', ['ROLE_TEACHER']);
        $client->request('PATCH', '/api/classes/'.$class->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['name' => '2C'],
        ]);
        $this->assertResponseStatusCodeSame(403);

        // tests if user with ROLE_ADMIN gets Success
        $this->createUserAndLogIn($client, 'admin@test.com', 'test', ['ROLE_ADMIN']);
        $client->request('PATCH', '/api/classes/'.$class->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['name' => '2C'],
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['name' => '2C']);
    }
}
