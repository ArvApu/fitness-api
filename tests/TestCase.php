<?php

use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    public function assertDatabaseHas(string $table, array $data): TestCase
    {
        return $this->seeInDatabase($table, $data);
    }

    public function assertDatabaseMissing(string $table, array $data): TestCase
    {
        return $this->missingFromDatabase($table, $data);
    }
}
