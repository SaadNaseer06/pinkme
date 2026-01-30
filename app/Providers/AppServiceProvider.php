<?php

namespace App\Providers;

use App\Events\MessageSent;
use App\Events\UserNotificationCreated;
use App\Http\Controllers\ChatMessageController;
use App\Mail\NewChatMessageEmail;
use App\Mail\UserNotificationEmail;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Send email when a user receives a notification
        Event::listen(UserNotificationCreated::class, function (UserNotificationCreated $event): void {
            $notification = $event->notification;
            $user = $notification->user ?? $notification->user()->first();
            if ($user && filled($user->email)) {
                Mail::to($user->email)->queue(new UserNotificationEmail($notification));
            }
        });

        // Send "you've got a new message" email only when the receiver is NOT currently in chat
        Event::listen(MessageSent::class, function (MessageSent $event): void {
            $message = $event->message;
            $receiver = $message->receiver ?? $message->receiver()->first();
            if (!$receiver || !filled($receiver->email)) {
                return;
            }
            if (ChatMessageController::isUserActiveInChat($receiver->id)) {
                return; // user is in chat, no email
            }
            Mail::to($receiver->email)->queue(new NewChatMessageEmail($message));
        });
    }
}
