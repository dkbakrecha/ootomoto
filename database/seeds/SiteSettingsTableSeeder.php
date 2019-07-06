<?php

use Illuminate\Database\Seeder;

class SiteSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('site_settings')->insert([
            'unique_key' => 'HELPEMAIL',
            'name' => 'Help Email',
            'value' => 'admin@flair.com',
            'group_id' => 0,
            'group_label' => 'General',
            'order' => 0,
        ]);
        
        DB::table('site_settings')->insert([
            'unique_key' => 'HELPCONTACT',
            'name' => 'Help Contact Number',
            'value' => '7894561230',
            'group_id' => 0,
            'group_label' => 'General',
            'order' => 1,
        ]);
        
        DB::table('site_settings')->insert([
            'unique_key' => 'TIMESHLOT',
            'name' => 'Time Slot (in minutes)',
            'value' => '20',
            'group_id' => 0,
            'group_label' => 'General',
            'order' => 2,
        ]);
        
        DB::table('site_settings')->insert([
            'unique_key' => 'REMINDERTIME',
            'name' => 'Amount of Time (in days)',
            'value' => '15',
            'group_id' => 1,
            'group_label' => 'Reminder Notification',
            'order' => 4,
        ]);
        
        DB::table('site_settings')->insert([
            'unique_key' => 'REMINDERTEXT',
            'name' => 'Content Text',
            'value' => 'Best services on best places',
            'group_id' => 1,
            'group_label' => 'Reminder Notification',
            'order' => 5,
        ]);
        
        DB::table('site_settings')->insert([
            'unique_key' => 'CANCELLATION_PERSENTAGE',
            'name' => 'Cancellation (%)',
            'value' => '2',
            'group_id' => 2,
            'group_label' => 'Cancellation',
            'order' => 6,
        ]);
    }
}
