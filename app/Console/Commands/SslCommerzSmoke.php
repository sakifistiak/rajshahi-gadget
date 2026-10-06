<?php

namespace App\Console\Commands;

use App\Support\SslCommerz;
use Illuminate\Console\Command;
use RuntimeException;

/**
 * Live check of the SSLCommerz credentials (Admin > Settings > Payment Gateway): opens a real payment
 * session for a 10 BDT test order (nothing is saved) and prints the payment
 * page URL. Run it right after adding the credentials, on sandbox first.
 */
class SslCommerzSmoke extends Command
{
    protected $signature = 'sslcommerz:smoke';

    protected $description = 'Open a test SSLCommerz payment session with the configured credentials';

    public function handle(): int
    {
        if (! SslCommerz::enabled()) {
            $this->error('SSLCommerz is off or has no credentials. Set them in Admin > Settings > Payment Gateway.');

            return self::FAILURE;
        }

        $credentials = SslCommerz::credentials();
        $mode = $credentials['sandbox'] ? 'SANDBOX' : 'LIVE';
        $this->info("Mode: {$mode} | Store ID: {$credentials['store_id']}");
        $this->line('Callback URLs: '.route('payment.sslcommerz.success').' (also /fail, /cancel, /ipn)');

        try {
            $url = SslCommerz::smokeTest();
        } catch (RuntimeException $e) {
            $this->error('Session init FAILED: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info('OK - session opened. Payment page:');
        $this->line($url);

        return self::SUCCESS;
    }
}
