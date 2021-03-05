<?php

use App\Events\SendMessage;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Laravel\Lumen\Testing\DatabaseMigrations;

class MessageTest extends TestCase
{
    use DatabaseMigrations;

    private $resource = '/messages';

    public function test_get_by_user()
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var User $receiver */
        $receiver = User::factory()->create();

        $sent = Message::factory()
            ->count(5)
            ->for($user, 'sender')
            ->for($receiver, 'receiver')
            ->create();

        $received = Message::factory()
            ->count(5)
            ->for($user, 'receiver')
            ->for($receiver, 'sender')
            ->create();

        $this->actingAs($user);

        $this->get("$this->resource/$receiver->id");

        $this->response->assertStatus(200);
        $this->response->assertJson($sent->merge($received)->toArray());
    }

    public function test_send()
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var User $receiver */
        $receiver = User::factory()->create();

        $this->actingAs($user);

        $payload = [
            'message' => 'Hello there.',
        ];

        $this->post("$this->resource/$receiver->id", $payload);

        $this->response->assertStatus(201);
        $this->response->assertJsonFragment($payload);

        $this->assertDatabaseHas((new Message())->getTable(), [
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'message' => $payload['message'],
        ]);
    }
}
