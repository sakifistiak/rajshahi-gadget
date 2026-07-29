<?php
namespace Database\Seeders;

use App\Models\CustomerSpotlight;
use Illuminate\Database\Seeder;

class CustomerSpotlightSeeder extends Seeder
{
    public function run(): void
    {
        $laptopPreowned = '/assets/laptop-preowned-CnekQJTH.jpg';
        $laptopUltrabook = '/assets/laptop-ultrabook-C5nU_6_f.jpg';
        $laptopGaming = '/assets/laptop-gaming-CRMi9E0Q.jpg';
        $laptopBusiness = '/assets/laptop-business-3Mb6HsAh.jpg';
        $accBag = '/assets/acc-bag-DUSyzYyb.jpg';
        $accKeyboard = '/assets/acc-keyboard-C2O0-ufI.jpg';

        $names = ['Rifat Hossain', 'Sadia Rahman', 'Imran Kabir', 'Nusrat Jahan', 'Tanvir Ahmed', 'Mahmuda Akter', 'Sabbir Islam', 'Rakib Chowdhury', 'Farhana Ali', 'Arif Mahmud'];
        $locations = ['Dhaka', 'Chattogram', 'Sylhet', 'Khulna', 'Rajshahi', 'Barishal', 'Cumilla', 'Mymensingh'];
        $images = [$laptopPreowned, $laptopUltrabook, $laptopGaming, $laptopBusiness, $accBag, $accKeyboard];
        $products = ['MacBook Pro 14 M1 Pro', 'ThinkPad X1 Carbon', 'Asus TUF Gaming F15', 'Dell Latitude 7420', 'Armor Laptop Backpack', 'Mechanical Keyboard TKL'];

        for ($t = 0; $t < 18; $t++) {
            CustomerSpotlight::create([
                'name' => $names[$t % count($names)],
                'location' => $locations[$t % count($locations)],
                'product' => $products[$t % count($products)],
                'image' => $images[$t % count($images)],
                'quote' => 'Boxing khulei mone holo showroom theke pelam. Ekdom fresh, warranty seal intact. Delivery same-day chilo!',
                'date' => sprintf('2026-%02d-1%d', ($t % 6) + 1, $t % 9),
            ]);
        }
    }
}
