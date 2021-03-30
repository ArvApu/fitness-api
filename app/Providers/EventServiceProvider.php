<?php

namespace App\Providers;

use App\Events\UserAcceptedInvitation;
use App\Events\UserDeleted;
use App\Events\UserProfileUpdated;
use App\Events\WorkoutLogged;
use App\Listeners\AddToNewsFeed;
use App\Listeners\CreateRoom;
use App\Listeners\LogWeight;
use App\Listeners\SendEmail;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UserProfileUpdated::class => [
            LogWeight::class,
            AddToNewsFeed::class,
        ],
        UserDeleted::class => [
            SendEmail::class,
        ],
        UserAcceptedInvitation::class => [
            AddToNewsFeed::class,
            CreateRoom::class,
        ],
        WorkoutLogged::class => [
            AddToNewsFeed::class,
        ],
    ];
}
