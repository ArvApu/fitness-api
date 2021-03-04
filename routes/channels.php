<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user.{id}', function (\App\Models\User $user, int $id) {
    return $user->id === $id;
});
