<?php

namespace Database\Seeders;

use App\Models\CustomPage;
use Illuminate\Database\Seeder;

class CustomPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CustomPage::firstOrCreate(
            ['slug' => 'warranty-policy'],
            [
                'title' => 'Warranty & Return Policy',
                'meta_title' => 'Warranty & Replacement Policy — Khan Gadget',
                'meta_description' => 'Official 7-day replacement and official warranty terms at Khan Gadget Bangladesh.',
                'content' => '
                    <h2 style="color: #2563eb; font-weight: 700; font-size: 1.25rem; margin-bottom: 0.75rem;">1. Warranty Coverage</h2>
                    <p>At <strong>Khan Gadget</strong>, all our Intact Box and Pre-Owned laptops and gadgets come with standard warranty coverage:</p>
                    <ul>
                        <li><strong>Brand New Intact Box:</strong> Official brand warranty as declared by manufacturers (Apple, Dell, Asus, HP, Lenovo).</li>
                        <li><strong>Pre-Owned / Certified Used:</strong> 7-day instant replacement guarantee and 1-year service warranty.</li>
                    </ul>

                    <h2 style="color: #2563eb; font-weight: 700; font-size: 1.25rem; margin-top: 1.5rem; margin-bottom: 0.75rem;">2. Replacement Conditions</h2>
                    <p>To claim a replacement within 7 days:</p>
                    <ol>
                        <li>The item must be in original condition with box, receipt, and serial numbers intact.</li>
                        <li>Physical damage, water damage, or unauthorized repair attempts are excluded from warranty claims.</li>
                    </ol>

                    <div style="background-color: #f8fafc; border-left: 4px solid #2563eb; padding: 1rem; margin-top: 1.5rem; border-radius: 0.375rem;">
                        <p style="margin: 0; font-weight: 600;">For support or warranty claims, contact our helpline at 01700-000000 or visit our Rajshahi / Dhaka store locations.</p>
                    </div>
                ',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );
    }
}
