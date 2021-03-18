<?php

use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations;

class MessageTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var string
     */
    private $resource = '/messages';

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

    public function test_send()
    {
        /** @var User $receiver */
        $receiver = User::factory()->create();

        /** @var Room $room */
        $room = Room::factory()->for($this->user, 'admin')
            ->hasAttached(collect([$this->user, $receiver]))
            ->create();

        $payload = [
            'message' => 'Hello there.',
        ];

        $this->post("$this->resource/$room->id", $payload);

        $this->response->assertStatus(201);
        $this->response->assertJsonFragment($payload);

        $this->assertDatabaseHas((new Message())->getTable(), [
            'user_id' => $this->user->id,
            'room_id' => $room->id,
            'message' => $payload['message'],
        ]);
    }
}
