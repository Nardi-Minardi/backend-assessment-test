<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DebitCard;

class DebitCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DebitCard::factory(5)->create();
    }
}
