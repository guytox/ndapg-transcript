<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentConfiguration;
use Illuminate\Support\Str;

class PaymentConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $purposes = ['Application Fee'];
        foreach ($purposes as $purpose) {
            PaymentConfiguration::firstOrCreate(['purpose' => $purpose], [
                'purpose' => $purpose,
                'payment_purpose_slug' => Str::slug(strtolower($purpose)),
                'status' => true,
                'amount' => 20000,
            ]);
        }
    }
}
