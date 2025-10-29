<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register any event broadcasting channels that your
| application supports. The given channel authorization callbacks
| are used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('users.{userId}.notifications', function ($user, int $userId): bool {
    return (int) $user->id === $userId;
});
