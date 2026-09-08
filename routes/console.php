<?php

use App\Models\ChatConversation;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Belt-and-braces: live chat conversations also auto-close lazily on every
// request that touches the chat tables (see ChatConversation::autoCloseStale()),
// but run it here too so it fires even with no traffic, if a scheduler is running.
Schedule::call(fn () => ChatConversation::autoCloseStale())->everyMinute();

// Flags idle carts as abandoned and sends SMS reminders (no-ops until a real
// SMS gateway is configured — see App\Support\Sms\LogSmsGateway). Relies on a
// real server cron running `php artisan schedule:run` every minute — same
// unverified assumption as the job above.
Schedule::command('cart:process-abandoned')->everyFiveMinutes();
