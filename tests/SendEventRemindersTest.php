<?php

use App\Console\Commands\SendEventReminders;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;

class SendEventRemindersTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var string
     */
    private $command = SendEventReminders::class;

    public function test_if_sends_reminders()
    {
        /** @var User $trainer */
        $trainer = User::factory()->trainer()->create();

        /** @var User $user1 */
        $user1 = User::factory()->for($trainer, 'trainer')->create();
        /** @var User $user1 */
        $user2 = User::factory()->for($trainer, 'trainer')->create();

        Event::factory()->for($user1, 'attendee')->create(['start_time' => Carbon::tomorrow()]);
        Event::factory()->for($user2, 'attendee')->create(['start_time' => Carbon::tomorrow()]);

        $code = $this->artisan($this->command);

        $this->assertEquals(0, $code);
    }
}
