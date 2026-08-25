<?php
namespace Database\Seeders;

use App\Models\PhilanthropicWork;
use Illuminate\Database\Seeder;

class PhilanthropicWorkSeeder extends Seeder
{
    public function run(): void
    {
        $works = [
            ['title' => 'Flood Relief · Sunamganj', 'image' => '/assets/banner-workspace-u7h5GKkP.jpg'],
            ['title' => 'Winter Clothing Drive', 'image' => '/assets/cat-wearables-WxrTS6bM.jpg'],
            ['title' => 'Iftar for Rickshaw Pullers', 'image' => '/assets/cat-phones-Dus4mqTU.jpg'],
            ['title' => 'Laptops for Students', 'image' => '/assets/laptop-business-3Mb6HsAh.jpg'],
            ['title' => 'Blood Donation Camp', 'image' => '/assets/cat-gaming-CcaZ9lay.jpg'],
            ['title' => 'Eid Gift Boxes for Orphans', 'image' => '/assets/hero-laptop-BB9a8YSR.jpg'],
        ];

        foreach ($works as $w) {
            PhilanthropicWork::create($w);
        }
    }
}
