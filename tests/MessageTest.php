<?php

use App\Models\Message;
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

    public function test_get_by_user()
    {
        /** @var User $receiver */
        $receiver = User::factory()->create();

        $sent = Message::factory()
            ->count(5)
            ->for($this->user, 'sender')
            ->for($receiver, 'receiver')
            ->create();

        $received = Message::factory()
            ->count(5)
            ->for($this->user, 'receiver')
            ->for($receiver, 'sender')
            ->create();

        $this->get("$this->resource/$receiver->id");

        $this->response->assertStatus(200);
        $this->response->assertJson($sent->merge($received)->toArray());
    }

    public function test_send()
    {
        /** @var User $receiver */
        $receiver = User::factory()->create();

        $payload = [
            'message' => 'Hello there.',
        ];

        $this->post("$this->resource/$receiver->id", $payload);

        $this->response->assertStatus(201);
        $this->response->assertJsonFragment($payload);

        $this->assertDatabaseHas((new Message())->getTable(), [
            'sender_id' => $this->user->id,
            'receiver_id' => $receiver->id,
            'message' => $payload['message'],
        ]);
    }
}
