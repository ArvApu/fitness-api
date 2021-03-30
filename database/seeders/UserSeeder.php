<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* Create trainers */
        $t1 = User::factory()->trainer()->create();
        $t2 = User::factory()->trainer()->create();

        /* Create client users for first trainer */
        $users1 = User::factory()->count(10)->for($t1, 'trainer')->create();

        /* Create client users for second trainer */
        $users2 = User::factory()->count(10)->for($t2, 'trainer')->create();

        /** @var User $user */
        foreach ($users1->merge($users2) as $user) {
            if($user->role !== 'user') {
                continue;
            }

            $room = $user->trainer->administratedRooms()->create([
                'name' => $user->full_name,
            ]);

            $room->users()->attach([$user->id, $user->trainer->id]);
        }
    }
}
