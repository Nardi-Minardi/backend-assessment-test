<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DebitCardTransaction;

class DebitCardTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DebitCardTransaction::factory(5)->create();
    }
}
