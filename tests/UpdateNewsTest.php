<?php

use App\Console\Commands\UpdateNews;
use App\Models\NewsEvent;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;

class UpdateNewsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var string
     */
    private $command = UpdateNews::class;

    public function test_if_adds_birthday_to_news()
    {
        /** @var User $trainer */
        $trainer = User::factory()->trainer()->create();

        /** @var User $user */
        $user = User::factory()->for($trainer, 'trainer')->create([
            'birthday' => Carbon::now(),
        ]);

        $this->artisan($this->command);

        $this->assertDatabaseHas((new NewsEvent)->getTable(), [
            'content' => "Today is $user->full_name's birthday",
            'user_id' => $user->trainer_id,
        ]);
    }
}
