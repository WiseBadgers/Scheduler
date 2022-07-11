<?php

declare(strict_types=1);

namespace App\Tests;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class SemesterTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testPostSemester(): void
    {
        $client = self::createClient();

        // tests if unauthorized user gets Unauthorized
        $client->request('POST', '/api/semesters', [
            'json' => ['name' => 'Summer 2022'],
        ]);
        $this->assertResponseStatusCodeSame(401);

        // tests if user without ROLE_ADMIN gets Access Denied
        $this->createUserAndLogIn($client, 'not-admin@test.com', 'test', ['ROLE_TEACHER']);
        $client->request('POST', '/api/semesters', [
            'json' => ['name' => 'Summer 2022'],
        ]);
        $this->assertResponseStatusCodeSame(403);

        // tests if user with ROLE_ADMIN gets Success
        $this->createUserAndLogIn($client, 'admin@test.com', 'test', ['ROLE_ADMIN']);
        $client->request('POST', '/api/semesters', [
            'json' => ['name' => 'Summer 2022'],
        ]);
        $this->assertResponseIsSuccessful();

        // test if name that contains more than 30 letters can be added
        $client->request('POST', '/api/semesters', [
            'json' => ['name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'],
        ]);
        $this->assertResponseStatusCodeSame(422);
    }

    public function testGetSemester(): void
    {
        $client = self::createClient();
        $semester = $this->createSemester('Winter 2021');

        // tests if anonymous user gets Unauthorized
        $client->request('GET', '/api/semesters/'.$semester->getId());
        $this->assertResponseStatusCodeSame(401);

        // tests if logged-in user gets Success
        $this->createUserAndLogIn($client, 'test@test.com', 'test', ['ROLE_TEACHER']);
        $client->request('GET', '/api/semesters/'.$semester->getId());
        $this->assertResponseIsSuccessful();
    }

    public function testDeleteSemester(): void
    {
        $client = $this->createClient();
        $semester = $this->createSemester('Winter 2021');

        // tests if anonymous user gets Unauthorized
        $client->request('DELETE', '/api/semesters/'.$semester->getId());
        $this->assertResponseStatusCodeSame(401);

        // tests if user without ROLE_ADMIN gets Access Denied
        $this->createUserAndLogIn($client, 'test@test.com', 'test', ['ROLE_TEACHER']);
        $client->request('DELETE', '/api/semesters/'.$semester->getId());
        $this->assertResponseStatusCodeSame(403);

        // tests if user with ROLE_ADMIN gets Success
        $this->createUserAndLogIn($client, 'admin@test.com', 'test', ['ROLE_ADMIN']);
        $client->request('DELETE', '/api/semesters/'.$semester->getId());
        $this->assertResponseIsSuccessful();
    }

    public function testPatchSemester(): void
    {
        $client = $this->createClient();
        $semester = $this->createSemester('Winter 2022');

        // tests if anonymous user gets Unauthorized
        $client->request('PATCH', '/api/semesters/'.$semester->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['name' => 'Summer 2023'],
        ]);
        $this->assertResponseStatusCodeSame(401);

        // tests if user without ROLE_ADMIN gets Access Denied
        $this->createUserAndLogIn($client, 'test@test.com', 'test', ['ROLE_TEACHER']);
        $client->request('PATCH', '/api/semesters/'.$semester->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['name' => 'Summer 2023'],
        ]);
        $this->assertResponseStatusCodeSame(403);

        // tests if user with ROLE_ADMIN gets Success
        $this->createUserAndLogIn($client, 'admin@test.com', 'test', ['ROLE_ADMIN']);
        $client->request('PATCH', '/api/semesters/'.$semester->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['name' => 'Summer 2023'],
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['name' => 'Summer 2023']);
    }
}
