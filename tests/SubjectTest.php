<?php

declare(strict_types=1);

namespace App\Tests;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class SubjectTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testPostSubject(): void
    {
        $client = self::createClient();

        // tests if unauthorized user gets Unauthorized
        $client->request('POST', '/api/subjects', [
            'json' => ['name' => 'history'],
        ]);
        $this->assertResponseStatusCodeSame(401);

        // tests if user without ROLE_ADMIN gets Access Denied
        $this->createUserAndLogIn($client, 'not-admin@test.com', 'test', []);
        $client->request('POST', '/api/subjects', [
            'json' => ['name' => 'history'],
        ]);
        $this->assertResponseStatusCodeSame(403);

        // tests if user with ROLE_ADMIN gets Success
        $this->createUserAndLogIn($client, 'admin@test.com', 'test', ['ROLE_ADMIN']);
        $client->request('POST', '/api/subjects', [
            'json' => ['name' => 'history'],
        ]);
        $this->assertResponseIsSuccessful();

        // test if name that contains more than 30 letters can be added
        $client->request('POST', '/api/subjects', [
           'json' => ['name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'],
        ]);
        $this->assertResponseStatusCodeSame(422);
    }
}
