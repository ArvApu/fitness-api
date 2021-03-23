<?php

use App\Models\User;
use App\Models\UserLog;
use Laravel\Lumen\Testing\DatabaseMigrations;

class UserLogTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var string
     */
    private $resource = '/users/logs';

    /**
     * @var User
     */
    private $user;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_get_all()
    {
        $logs = UserLog::factory()
            ->count(10)
            ->for($this->user, 'user')
            ->create();

        $this->get($this->resource);

        $this->response->assertStatus(200);
        $this->response->assertJson([
            'data' => $logs->toArray()
        ]);
    }
}
