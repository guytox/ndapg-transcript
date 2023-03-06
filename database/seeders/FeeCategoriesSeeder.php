<?php

namespace Database\Seeders;

use App\Models\FeeCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FeeCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = ['Initial System Billing', 'System Late Registration Billing', 'Wallet Fund','acceptance_fee', 'application_fee', 'first_tuition', 'late_registration', 'tuition','portal_services'];
        foreach ($categories as $v ) {

            $catetory = FeeCategory::updateOrCreate( ['category_name' => $v], [
                'category_name' => $v,
                'description' => Str::slug(strtolower($v)),
                'payment_purpose_slug' => Str::slug(strtolower($v)),
                'status' => true

            ]);

            }

    }
}
