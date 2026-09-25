<?php

namespace Tests\Unit;

use App\Support\GoogleForm;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class GoogleFormTest extends TestCase
{
    private const ID = '1FAIpQLSf-abc_123';

    private const EMBED = 'https://docs.google.com/forms/d/e/'.self::ID.'/viewform?embedded=true';

    /** @return array<string, array{0: string, 1: string}> */
    public static function accepted(): array
    {
        return [
            'share link' => ['https://docs.google.com/forms/d/e/'.self::ID.'/viewform?usp=sf_link', self::EMBED],
            'share link, no query' => ['https://docs.google.com/forms/d/e/'.self::ID.'/viewform', self::EMBED],
            'already embedded' => [self::EMBED, self::EMBED],
            'account prefix' => ['https://docs.google.com/forms/u/0/d/e/'.self::ID.'/viewform', self::EMBED],
            'surrounding spaces' => ['  '.self::EMBED."\n", self::EMBED],
            'older non-published id' => ['https://docs.google.com/forms/d/'.self::ID.'/viewform', 'https://docs.google.com/forms/d/'.self::ID.'/viewform?embedded=true'],
            'embed code' => [
                '<iframe src="https://docs.google.com/forms/d/e/'.self::ID.'/viewform?embedded=true" width="640" height="1377" frameborder="0" marginheight="0" marginwidth="0">Loading…</iframe>',
                self::EMBED,
            ],
            'prefilled answers keep their dotted keys' => [
                'https://docs.google.com/forms/d/e/'.self::ID.'/viewform?usp=pp_url&entry.123=Dhaka',
                'https://docs.google.com/forms/d/e/'.self::ID.'/viewform?entry.123=Dhaka&embedded=true',
            ],
            'prefilled answers in embed code' => [
                '<iframe src="https://docs.google.com/forms/d/e/'.self::ID.'/viewform?entry.123=Dhaka&amp;embedded=true"></iframe>',
                'https://docs.google.com/forms/d/e/'.self::ID.'/viewform?entry.123=Dhaka&embedded=true',
            ],
        ];
    }

    #[DataProvider('accepted')]
    public function test_it_returns_the_embeddable_form_url(string $input, string $expected): void
    {
        $this->assertSame($expected, GoogleForm::embedUrl($input));
    }

    /** @return array<string, array{0: ?string}> */
    public static function rejected(): array
    {
        return [
            'empty' => [''],
            'null' => [null],
            'owner edit link' => ['https://docs.google.com/forms/d/'.self::ID.'/edit'],
            'short link' => ['https://forms.gle/abc123'],
            'plain http' => ['http://docs.google.com/forms/d/e/'.self::ID.'/viewform'],
            'other google doc' => ['https://docs.google.com/document/d/'.self::ID.'/edit'],
            'lookalike host' => ['https://docs.google.com.evil.test/forms/d/e/'.self::ID.'/viewform'],
            'credentials in url' => ['https://x@docs.google.com/forms/d/e/'.self::ID.'/viewform'],
            'javascript url' => ['javascript:alert(1)'],
            'other iframe' => ['<iframe src="https://evil.test/forms/d/e/'.self::ID.'/viewform"></iframe>'],
            'iframe without src' => ['<iframe></iframe>'],
        ];
    }

    #[DataProvider('rejected')]
    public function test_anything_but_a_public_google_form_is_rejected(?string $input): void
    {
        $this->assertNull(GoogleForm::embedUrl($input));
    }
}
