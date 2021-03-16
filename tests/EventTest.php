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
     * @var User
     */
    private $trainer;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->trainer = User::factory()->trainer()->create();
        $this->user = User::factory()->for($this->trainer, 'trainer')->create();
        $this->actingAs($this->user);
    }

    public function test_get_all()
    {
        $events = Event::factory()
            ->for($this->user, 'attendee')
            ->count(10)
            ->create();

        $this->get($this->resource.'?start_date='.
            Carbon::yesterday()->toDateString() .'&end_date='.
            Carbon::tomorrow()->toDateString()
        );

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

    public function test_export()
    {
         Event::factory()
            ->for($this->user, 'attendee')
            ->count(10)
            ->create();

        $this->get($this->resource.'/export');

        $this->response->assertStatus(200);

        $this->assertEquals(
            'text/calendar; charset=UTF-8',
            $this->response->headers->get('Content-Type')
        );

        $this->assertEquals(
            'attachment; filename="calendar.ics"',
            $this->response->headers->get('Content-Disposition')
        );
    }

    public function test_get_all_for_trainer()
    {
        $this->actingAs($this->trainer);

        $events = Event::factory()
            ->for($this->user, 'attendee')
            ->count(10)
            ->create();

        $this->get($this->resource.'?start_date='.
            Carbon::yesterday()->toDateString().'&end_date='.
            Carbon::tomorrow()->toDateString().'&user_id='.$this->user->id
        );

        $this->response->assertStatus(200);
        $this->response->assertJson($events->toArray());
    }

    public function test_get_all_for_trainer_without_specifying_client()
    {
        $this->actingAs($this->trainer);

        Event::factory()
            ->for($this->user, 'attendee')
            ->count(10)
            ->create();

        $this->get($this->resource.'?start_date='.
            Carbon::yesterday()->toDateString().'&end_date='.
            Carbon::tomorrow()->toDateString()
        );

        $this->response->assertStatus(422);
    }

    public function test_forbid_get_all_for_invalid_trainer()
    {
        $this->actingAs(User::factory()->trainer()->create());

        Event::factory()
            ->for($this->user, 'attendee')
            ->count(10)
            ->create();

        $this->get($this->resource.'?start_date='.
            Carbon::yesterday()->toDateString().'&end_date='.
            Carbon::tomorrow()->toDateString().'&user_id='.$this->user->id
        );

        $this->response->assertStatus(403);
    }

    public function test_get_all_for_admin()
    {
        $this->actingAs(User::factory()->admin()->create());

        $events = Event::factory()
            ->for($this->user, 'attendee')
            ->count(10)
            ->create();

        $this->get($this->resource.'?start_date='.
            Carbon::yesterday()->toDateString().'&end_date='.
            Carbon::tomorrow()->toDateString().'&user_id='.$this->user->id
        );

        $this->response->assertStatus(200);
        $this->response->assertJson($events->toArray());
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
