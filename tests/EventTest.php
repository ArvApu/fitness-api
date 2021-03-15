<?php

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;

class EventTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var string
     */
    private $resource = '/events';

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
        $events = Event::factory()
            ->for($this->user, 'attendee')
            ->count(10)
            ->create();

        $this->get($this->resource);

        $this->response->assertStatus(200);
        $this->response->assertJson($events->toArray());
    }

    public function test_get_single()
    {
        /** @var Event $event */
        $event = Event::factory()->for($this->user, 'attendee')->create();

        $this->get("$this->resource/$event->id");

        $this->response->assertStatus(200);
        $this->response->assertJson($event->toArray());
    }

    public function test_store()
    {
        $payload = [
            'attendee_id' => $this->user->id,
            'title' => 'Test event',
            'information' => 'This is only for testing',
            'start_time' => Carbon::now()->toDateTimeString(),
            'end_time' => Carbon::now()->addMinutes(60)->toDateTimeString(),
        ];

        $this->post($this->resource, $payload);

        $this->response->assertStatus(201);
        $this->response->assertJsonFragment($payload);

        $this->assertDatabaseHas((new Event)->getTable(), $payload);
    }

    public function test_update()
    {
        /** @var Event $event */
        $event = Event::factory()->for($this->user, 'attendee')->create();

        $payload = [
            'title' => 'Test event',
            'information' => 'This is only for testing',
        ];

        $this->put("$this->resource/$event->id", $payload);

        $this->response->assertStatus(200);
        $this->response->assertJsonFragment($payload);

        $table = (new Event)->getTable();

        $this->assertDatabaseHas($table, $payload);
        $this->assertDatabaseMissing($table, $event->toArray());
    }

    public function test_delete()
    {
        /** @var Event $event */
        $event = Event::factory()->for($this->user, 'attendee')->create();

        $this->delete("$this->resource/$event->id");

        $this->response->assertStatus(204);
        $this->assertDatabaseMissing((new Event())->getTable(), $event->toArray());
    }
}
