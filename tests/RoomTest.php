<?php

use App\Models\Message;
use App\Models\User;
use App\Models\Room;
use Laravel\Lumen\Testing\DatabaseMigrations;

class RoomTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var string
     */
    private $resource = '/rooms';

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
        $rooms = Room::factory()
            ->count(10)
            ->hasAttached($this->user)
            ->for($this->user, 'admin')
            ->create();

        $this->get($this->resource);

        $this->response->assertStatus(200);
        $this->response->assertJson([
            'data' => $rooms->toArray()
        ]);
    }

    public function test_get_messages()
    {
        /** @var Room $room */
        $room = Room::factory()
            ->hasAttached($this->user)
            ->for($this->user, 'admin')
            ->create();

        /** @var Message $messages */
        $messages = Message::factory()
            ->count(10)
            ->for($this->user, 'user')
            ->for($room, 'room')
            ->create();

        $this->get("$this->resource/$room->id/messages");

        $this->response->assertStatus(200);
        $this->response->assertJson([
            'data' => $messages->toArray()
        ]);
    }

    public function test_store()
    {
        $payload = [
            'name' => 'Test room',
            'users' => [
                $this->user->id, User::factory()->create()->id
            ],
        ];

        $this->post($this->resource, $payload);

        $this->response->assertStatus(201);
        $this->response->assertJsonFragment(['name' => $payload['name']]);

        $this->assertDatabaseHas((new Room)->getTable(), ['name' => $payload['name']]);
    }


    public function test_update()
    {
        /** @var Room $room */
        $room = Room::factory()
            ->hasAttached($this->user)
            ->for($this->user, 'admin')
            ->create();

        $payload = [
            'name' => 'Test room update',
        ];

        $this->put("$this->resource/$room->id", $payload);

        $this->response->assertStatus(200);
        $this->response->assertJsonFragment($payload);

        $table = (new Room)->getTable();

        $this->assertDatabaseHas($table, $payload);
        $this->assertDatabaseMissing($table, $room->toArray());
    }

    public function test_delete()
    {
        /** @var Room $room */
        $room = Room::factory()
            ->hasAttached($this->user)
            ->for($this->user, 'admin')
            ->create();

        $this->delete("$this->resource/$room->id");

        $this->response->assertNoContent();
        $this->assertDatabaseMissing((new Room)->getTable(), $room->toArray());
    }
}
