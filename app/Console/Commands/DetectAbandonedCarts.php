<?php

namespace App\Console\Commands;

use App\Contracts\SmsGateway;
use App\Models\AbandonedCart;
use App\Models\SiteSetting;
use App\Support\PhoneNumber;
use Illuminate\Console\Command;

class DetectAbandonedCarts extends Command
{
    protected $signature = 'cart:process-abandoned';

    protected $description = 'Flag idle carts as abandoned and send SMS reminders where a phone number was captured';

    public function handle(SmsGateway $sms): int
    {
        if (SiteSetting::getValue('cart_abandonment_enabled', '0') !== '1') {
            return self::SUCCESS;
        }

        $thresholdMinutes = (int) SiteSetting::getValue('cart_abandonment_threshold_minutes', '60');
        $cooldownHours = (int) SiteSetting::getValue('cart_abandonment_resend_cooldown_hours', '24');

        $flagged = AbandonedCart::where('status', AbandonedCart::STATUS_ACTIVE)
            ->whereNotNull('phone')
            ->where('last_activity_at', '<', now()->subMinutes($thresholdMinutes))
            ->update(['status' => AbandonedCart::STATUS_ABANDONED]);

        $this->info("Flagged {$flagged} cart(s) as abandoned.");

        $template = SiteSetting::getValue('cart_abandonment_sms_template', $this->defaultTemplate());

        $candidates = AbandonedCart::where('status', AbandonedCart::STATUS_ABANDONED)
            ->whereNotNull('phone')
            ->where(function ($q) use ($cooldownHours) {
                $q->whereNull('reminded_at')
                    ->orWhere('reminded_at', '<', now()->subHours($cooldownHours));
            })
            ->get();

        $sent = 0;
        foreach ($candidates as $cart) {
            $message = strtr($template, [
                '{name}' => $cart->customer_name ?: 'there',
                '{items}' => collect($cart->items)->pluck('name')->filter()->implode(', '),
                '{value}' => number_format($cart->cart_value),
            ]);

            if ($sms->send(PhoneNumber::tel($cart->phone), $message)) {
                $cart->update(['status' => AbandonedCart::STATUS_REMINDED, 'reminded_at' => now()]);
                $sent++;
            }
        }

        $this->info("Sent {$sent} reminder(s) (0 is expected until a real SMS gateway is configured).");

        return self::SUCCESS;
    }

    private function defaultTemplate(): string
    {
        return "Hi {name}, you left {items} (৳{value}) in your cart at Khan Gadget. Complete your order before it's gone!";
    }
}
