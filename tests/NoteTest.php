<?php

declare(strict_types=1);

namespace App\Tests;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class NoteTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateNote()
    {
        // created the client that will make requests to API
        $client = self::createClient();

        // POST note without login to check if Response has 401 (unauthorized) status
        $client->request('POST', '/api/notes', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [],
        ]);
        $this->assertResponseStatusCodeSame(401);

        // create STUDENT and log in
        $this->createUserAndLogIn(
            $client,
            'student@test.com',
            'test',
            ['ROLE_STUDENT']
        );

        // POST note as a student to see if Response has 403 (access denied) status
        $client->request('POST', '/api/notes', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['value' => 1],
        ]);
        $this->assertResponseStatusCodeSame(403);

        // create a TEACHER and log in
        $this->createUserAndLogIn(
            $client,
            'teacher@test.com',
            'test',
            ['ROLE_TEACHER']
        );

        // POST note as a teacher to see if Response has 201 (created) status
        $client->request('POST', '/api/notes', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['value' => 1],
        ]);
        $this->assertResponseStatusCodeSame(201);
    }
}
