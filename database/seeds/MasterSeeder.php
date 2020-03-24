<?php

use Illuminate\Database\Seeder;

class MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Master::create([
            'harga_m2_msy'  => '4000',
            'harga_m2_brh'  => '4500',
            'beban'  => '5000',
        ]);
    }
}
