<?php
namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'quote' => 'The delivery was faster than I expected and the laptop was exactly the grade they promised. I have already ordered twice more.',
                'author_name' => 'Rifat H.',
                'role' => 'Verified buyer · Dhaka',
                'sort_order' => 1,
            ],
            [
                'quote' => 'Prices matched the big stores and the support team actually answered on a Friday night. That is rare.',
                'author_name' => 'Sadia R.',
                'role' => 'Verified buyer · Chattogram',
                'sort_order' => 2,
            ],
            [
                'quote' => 'I bought a pre-owned ThinkPad, a RAM upgrade and a bag. Everything showed up together, next morning.',
                'author_name' => 'Imran K.',
                'role' => 'Verified buyer · Sylhet',
                'sort_order' => 3,
            ],
        ];

        foreach ($testimonials as $t) {
            Testimonial::create($t);
        }
    }
}
