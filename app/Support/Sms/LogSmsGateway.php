<?php

namespace App\Support\Sms;

use App\Contracts\SmsGateway;
use Illuminate\Support\Facades\Log;

/**
 * Default SMS gateway when no real provider is configured yet
 * (SMS_GATEWAY_DRIVER=log). Logs what would have been sent and always
 * returns false — callers must never treat this as a successful send.
 */
class LogSmsGateway implements SmsGateway
{
    public function send(string $phoneE164, string $message): bool
    {
        Log::info("[SMS:noop] Would send to {$phoneE164}: {$message}");

        return false;
    }
}
