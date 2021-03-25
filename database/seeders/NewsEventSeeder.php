<?php

namespace Database\Seeders;

use App\Models\NewsEvent;
use App\Models\User;
use Illuminate\Database\Seeder;

class NewsEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = (new User)->where('role', '='. 'trainer')->first();

        if($user === null) {
            $user = User::factory()->trainer()->create();
        }

        NewsEvent::factory()->for($user, 'user')->count(10)->create();
    }
}
