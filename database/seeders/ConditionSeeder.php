<?php
namespace Database\Seeders;

use App\Models\Condition;
use Illuminate\Database\Seeder;

class ConditionSeeder extends Seeder
{
    public function run(): void
    {
        $conditions = [
            ['slug' => 'intact', 'label' => 'BRAND NEW INTACT BOX', 'short' => 'INT', 'tagline' => 'Factory sealed. Full warranty.'],
            ['slug' => 'without-box', 'label' => 'BRAND NEW WITHOUT BOX', 'short' => 'WOB', 'tagline' => 'Brand new. Original box unavailable.'],
            ['slug' => 'pre-owned', 'label' => 'PRE-OWNED', 'short' => 'PRE', 'tagline' => 'Certified pre-owned. Tested & graded.'],
        ];

        foreach ($conditions as $c) {
            Condition::create($c);
        }
    }
}
