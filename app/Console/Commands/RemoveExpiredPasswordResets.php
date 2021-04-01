<?php

namespace App\Console\Commands;

use App\Models\PasswordReset;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class RemoveExpiredPasswordResets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'password-reset:remove-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes all password resets that are expired.';

    /**
     * @var PasswordReset
     */
    private $passwordReset;

    /**
     * Create a new command instance.
     *
     * @param PasswordReset $passwordReset
     */
    public function __construct(PasswordReset $passwordReset)
    {
        parent::__construct();
        $this->passwordReset = $passwordReset;
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        $this->passwordReset->where('expires_at', '<', Carbon::now())->delete();
        $this->info('Expired password resets removed.');
    }
}
