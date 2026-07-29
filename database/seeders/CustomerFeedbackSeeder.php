<?php
namespace Database\Seeders;

use App\Models\CustomerFeedback;
use Illuminate\Database\Seeder;

class CustomerFeedbackSeeder extends Seeder
{
    public function run(): void
    {
        $laptopUltrabook = '/assets/laptop-ultrabook-C5nU_6_f.jpg';
        $laptopGaming = '/assets/laptop-gaming-CRMi9E0Q.jpg';
        $laptopBusiness = '/assets/laptop-business-3Mb6HsAh.jpg';
        $laptopPreowned = '/assets/laptop-preowned-CnekQJTH.jpg';
        $accMouse = '/assets/acc-mouse-BbNzBi1z.jpg';
        $accRam = '/assets/acc-ram-bHl5zo9i.jpg';
        $accHeadphone = '/assets/acc-headphone-B-geqvKK.jpg';

        // Featured feedbacks
        $featured = [
            ['name' => 'Tanvir Ahmed', 'location' => 'Dhanmondi, Dhaka', 'rating' => 5.0, 'message' => 'MacBook Pro 14 M4 Intact Box order korchilam raat 10 tay, porer din dupur-e Dhanmondi-te delivery. Unboxing video-o diyeche courier bhai. Ei level-er service Bangladesh-e rare!', 'date' => '2026-07-24', 'image' => $laptopUltrabook],
            ['name' => 'Nusrat Jahan', 'location' => 'Agrabad, Chattogram', 'rating' => 5.0, 'message' => 'Pre-owned MacBook Air M2 niyechi — condition ekdom brand new-er moto. Battery health 94%, charger sob chilo. Khan Gadget-er grading 100% accurate.', 'date' => '2026-07-22', 'image' => $laptopPreowned],
            ['name' => 'Rifat Hossain', 'location' => 'Uttara, Dhaka', 'rating' => 5.0, 'message' => 'MacBook Air M3 Without Box niyechi 14k kom-e. Box nai bola-i chilo, baki sob brand new. 1 year warranty-o dise. Trusted!', 'date' => '2026-07-20', 'image' => $laptopBusiness],
            ['name' => 'Sabrina Rahman', 'location' => 'Sylhet Sadar', 'rating' => 5.0, 'message' => 'ThinkPad T14 Sylhet-e outside Dhaka delivery ekdom on time. Packaging double layer, kono scratch nai. Compare feature use kore 3ta model dekhe kinlam.', 'date' => '2026-07-18', 'image' => $laptopBusiness],
            ['name' => 'Mahmudul Karim', 'location' => 'Bashundhara, Dhaka', 'rating' => 4.5, 'message' => 'Asus ROG Strix G16 Intact Box + gaming mouse niyechi EMI-te. 0% EMI processing khub smooth, ek din-e approve. Gaming setup complete!', 'date' => '2026-07-15', 'image' => $laptopGaming],
            ['name' => 'Ayesha Siddika', 'location' => 'Mirpur, Dhaka', 'rating' => 5.0, 'message' => 'Laptop-er sathe 16GB RAM + keyboard + mousepad combo niyechi. Free installation kore diyeche, delivery free chilo Dhaka-r moddhe. Highly recommended.', 'date' => '2026-07-12', 'image' => $accRam],
        ];

        foreach ($featured as $f) {
            CustomerFeedback::create($f);
        }

        // Generated feedbacks
        $names = ['Adnan H.', 'Sumaiya A.', 'Rakib C.', 'Tania I.', 'Mahin F.', 'Zaria K.', 'Sohel M.', 'Ishrat J.', 'Naimul H.', 'Fariha N.'];
        $locations = ['Dhaka', 'Chattogram', 'Sylhet', 'Khulna', 'Rajshahi', 'Bogura', 'Narayanganj'];
        $messages = [
            'Ordered korlam 11 PM, next day noon-e delivery pelam. Packaging showroom-er cheye better.',
            'Pre-owned ThinkPad niyechi, condition 9.5/10. Battery health 92%. Price beshi valo.',
            'Support team WhatsApp-e reply diyeche 2 minute-e. Warranty claim easy chilo.',
            'MacBook Air M2 kine ekdom happy. EMI-o smooth chilo, extra charge nai.',
            'Delivery man bhalo, box khule check korte diyeche. Ei bharoshai bar bar kinbo.',
            'Trusted seller. Brand New Intact Box laptop exactly seal-korano peyechi.',
        ];
        $images = [$laptopPreowned, $laptopUltrabook, $laptopGaming, $laptopBusiness, $accMouse, $accHeadphone];

        for ($t = 0; $t < 22; $t++) {
            CustomerFeedback::create([
                'name' => $names[$t % count($names)],
                'location' => $locations[$t % count($locations)],
                'rating' => ($t % 3 === 0) ? 5.0 : 4.5,
                'message' => $messages[$t % count($messages)],
                'date' => sprintf('2026-%02d-%02d', ($t % 9) + 1, ($t % 27) + 1),
                'image' => $images[$t % count($images)],
            ]);
        }
    }
}
