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
                'excerpt' => 'Battery health, chassis grade, keyboard wear and a 42-point test — here is exactly what happens before a used laptop goes on sale.',
                'content' => json_encode([
                    'A pre-owned laptop is only as good as the test it survived. Every machine we sell goes through a 42-point inspection before it earns a grade.',
                    'We check battery cycle count and health, thermals under a 30-minute load, keyboard and trackpad response, port function, screen uniformity and hinge tension.',
                    'The grade you see on the listing — A, A+ or B — is the same grade printed on your invoice, so there are no surprises when you open the box.',
                ]),
                'category_tag' => 'Guides',
                'featured_image' => '/assets/laptop-preowned-CnekQJTH.jpg',
                'read_minutes' => 6,
                'author_name' => 'Ariq Rahman',
                'author_role' => 'Editor',
                'published_at' => '2026-02-12',
            ],
            [
                'slug' => 'intact-box-vs-without-box',
                'title' => 'Intact Box vs Without Box: what actually differs',
                'excerpt' => 'Both are brand new. One saves you real money. Here is what changes and what stays exactly the same.',
                'content' => json_encode([
                    'Brand New Intact Box means a factory sealed retail carton — untouched, with every printed accessory inside.',
                    'Brand New Without Box is the same unused machine, but the original retail carton is not available. You still get the charger, the warranty and an unused laptop for noticeably less.',
                ]),
                'category_tag' => 'Buying guide',
                'featured_image' => '/assets/laptop-ultrabook-C5nU_6_f.jpg',
                'read_minutes' => 4,
                'author_name' => 'Nadia Karim',
                'author_role' => 'Reviewer',
                'published_at' => '2026-01-28',
            ],
            [
                'slug' => 'cheapest-upgrades-for-an-old-laptop',
                'title' => 'Three cheap upgrades that revive an old laptop',
                'excerpt' => 'A RAM stick, an SSD and a proper cooling habit will beat a brand new budget machine almost every time.',
                'content' => json_encode([
                    'Most laptops that feel slow are not short on CPU — they are short on memory. Going from 8 GB to 16 GB is the single biggest change you can buy.',
                    'After RAM, a fast NVMe SSD and a clean fan will do more for daily speed than any new mid-range purchase.',
                ]),
                'category_tag' => 'Upgrades',
                'featured_image' => '/assets/acc-ram-bHl5zo9i.jpg',
                'read_minutes' => 5,
                'author_name' => 'Tanvir Ahmed',
                'author_role' => 'Contributor',
                'published_at' => '2026-01-14',
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::create($post);
        }
    }
}
