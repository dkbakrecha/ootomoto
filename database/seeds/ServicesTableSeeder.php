<?php

use Illuminate\Database\Seeder;

class ServicesTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $services = ['Head shave', 'Haircut', 'Buzzcut', 'Beardtrim', 'Mustache trim', 'Lineup', 'Hot Towel Shave'];
        foreach ($services as $key => $val) {
            DB::table('services')->insert([
                'name' => $val,
                'unique_id' => 'SR0000' . ($key + 1),
                'category_id' => rand(1, 5),
                'duration' => rand(60, 120),
                'price' => rand(200, 500),
            ]);
        }
    }

}
