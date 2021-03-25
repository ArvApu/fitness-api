<?php

use App\Models\NewsEvent;
use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;

class NewsEventTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var string
     */
    private $resource = '/news';

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
        $this->user = User::factory()->trainer()->create();
        $this->actingAs($this->user);
    }

    public function test_get_all()
    {
        $events = NewsEvent::factory()
            ->for($this->user, 'user')
            ->count(10)
            ->create();

        $this->get($this->resource);

        $this->response->assertStatus(200);

        $this->assertEquals(
            $events->sortByDesc('created_at')->pluck('id')->values(),
            collect(json_decode($this->response->getContent())->data)->pluck('id')->values())
        ;
    }
}
