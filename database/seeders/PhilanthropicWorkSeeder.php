<?php
namespace Database\Seeders;

use App\Models\PhilanthropicWork;
use Illuminate\Database\Seeder;

class PhilanthropicWorkSeeder extends Seeder
{
    public function run(): void
    {
        $works = [
            ['title' => 'Flood Relief · Sunamganj', 'place' => 'Sunamganj', 'image' => '/assets/banner-workspace-u7h5GKkP.jpg', 'summary' => 'Distributed food, medicine and clean water to 320 flood-affected families with local volunteers.', 'date' => '2025-08-14'],
            ['title' => 'Winter Clothing Drive', 'place' => 'Rangpur', 'image' => '/assets/cat-wearables-WxrTS6bM.jpg', 'summary' => 'Handed out warm clothing packs to 500+ people during the coldest week of the season.', 'date' => '2025-12-22'],
            ['title' => 'Iftar for Rickshaw Pullers', 'place' => 'Dhaka', 'image' => '/assets/cat-phones-Dus4mqTU.jpg', 'summary' => 'Daily iftar distribution to over 200 rickshaw pullers throughout the month of Ramadan.', 'date' => '2026-03-10'],
            ['title' => 'Laptops for Students', 'place' => 'Cumilla', 'image' => '/assets/laptop-business-3Mb6HsAh.jpg', 'summary' => 'Donated 24 refurbished laptops to meritorious students from underserved families.', 'date' => '2026-01-06'],
            ['title' => 'Blood Donation Camp', 'place' => 'Chattogram', 'image' => '/assets/cat-gaming-CcaZ9lay.jpg', 'summary' => 'Hosted a community blood donation event with 90+ donors and Red Crescent partners.', 'date' => '2025-11-19'],
            ['title' => 'Eid Gift Boxes for Orphans', 'place' => 'Dhaka', 'image' => '/assets/hero-laptop-BB9a8YSR.jpg', 'summary' => 'Delivered new clothing, snacks and toys as Eid gifts to 240 children across three orphanages.', 'date' => '2026-04-05'],
        ];

        foreach ($works as $w) {
            PhilanthropicWork::create($w);
        }
    }
}
