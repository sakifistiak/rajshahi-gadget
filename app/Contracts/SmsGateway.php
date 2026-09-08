<?php

namespace App\Contracts;

interface SmsGateway
{
    /**
     * Send an SMS. Must return true only when the message was actually
     * accepted by a real provider — callers rely on this to decide whether
     * a reminder may be marked as sent.
     */
    public function send(string $phoneE164, string $message): bool;
}
