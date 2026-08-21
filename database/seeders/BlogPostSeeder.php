<?php
namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'slug' => 'how-we-grade-pre-owned-laptops',
                'title' => 'How we grade every pre-owned laptop',
                'content' => '<p>A pre-owned laptop is only as good as the test it survived. Every machine we sell goes through a 42-point inspection before it earns a grade.</p><p>We check battery cycle count and health, thermals under a 30-minute load, keyboard and trackpad response, port function, screen uniformity and hinge tension.</p><p>The grade you see on the listing — A, A+ or B — is the same grade printed on your invoice, so there are no surprises when you open the box.</p>',
                'featured_image' => '/assets/laptop-preowned-CnekQJTH.jpg',
                'published_at' => '2026-02-12',
            ],
            [
                'slug' => 'intact-box-vs-without-box',
                'title' => 'Intact Box vs Without Box: what actually differs',
                'content' => '<p>Brand New Intact Box means a factory sealed retail carton — untouched, with every printed accessory inside.</p><p>Brand New Without Box is the same unused machine, but the original retail carton is not available. You still get the charger, the warranty and an unused laptop for noticeably less.</p>',
                'featured_image' => '/assets/laptop-ultrabook-C5nU_6_f.jpg',
                'published_at' => '2026-01-28',
            ],
            [
                'slug' => 'cheapest-upgrades-for-an-old-laptop',
                'title' => 'Three cheap upgrades that revive an old laptop',
                'content' => '<p>Most laptops that feel slow are not short on CPU — they are short on memory. Going from 8 GB to 16 GB is the single biggest change you can buy.</p><p>After RAM, a fast NVMe SSD and a clean fan will do more for daily speed than any new mid-range purchase.</p>',
                'featured_image' => '/assets/acc-ram-bHl5zo9i.jpg',
                'published_at' => '2026-01-14',
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::create($post);
        }
    }
}
