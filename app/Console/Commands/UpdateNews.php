<?php

namespace App\Console\Commands;

use App\Models\NewsEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates news list.';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Exception
     */
    public function handle(): void
    {
        $this->addBirthdays();
        $this->info('Finished news update.');
    }

    /**
     * Add birthdays to news
     */
    private function addBirthdays(): void
    {
         $users = (new User)->whereNotNull('birthday')
             ->whereMonth('birthday', '=', Carbon::now()->format('m'))
             ->whereDay('birthday', '=', Carbon::now()->format('d'))
             ->get();

         $news = [];

         /** @var User $user */
         foreach ($users as $user) {
             if($user->trainer_id === null) {
                 continue;
             }
             $news = [
                 'content' => "Today is $user->full_name's birthday",
                 'user_id' => $user->trainer_id,
                 'created_at' => Carbon::now(),
             ];
         }

         (new NewsEvent())->insert($news);
         $this->comment('Birthdays added.');
    }
}
