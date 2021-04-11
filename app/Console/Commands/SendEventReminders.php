<?php

namespace App\Console\Commands;

use App\Mail\RemindEvent;
use App\Models\Event;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Mail\Mailer;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends reminders to users.';

    /**
     * @var Event
     */
    private $event;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * SendReminders constructor.
     * @param Event $event
     * @param Mailer $mailer
     */
    public function __construct(Event $event, Mailer $mailer)
    {
        parent::__construct();
        $this->event = $event;
        $this->mailer = $mailer;
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        $events = $this->event->whereDate('start_time', '=', Carbon::tomorrow())->get();

        /** @var Event $event */
        foreach ($events as $event) {
            $this->mailer->to($event->attendee->email)->send(new RemindEvent($event));
        }

        $this->info('Finished sending reminders.');
    }
}
