<?php

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        Setting::firstOrCreate([
            'key' => 'appStatus',
        ], [
            'value' => true,
            'type' => 'checkbox',
            'display_name' => 'حالة التطبيق'
        ]);

        Setting::firstOrCreate([
            'key' => 'twitter',
        ], [
            'value' => 'https://twitter.com/',
            'type' => 'text',
            'display_name' => 'موقع تويتر'
        ]);

        Setting::firstOrCreate([
            'key' => 'facebook',
        ], [
            'value' => 'https://www.facebook.com/',
            'type' => 'text',
            'display_name' => 'موقع فيس بوك'
        ]);

        Setting::firstOrCreate([
            'key' => 'snapchat',
        ], [
            'value' => 'https://www.snapchat.com/',
            'type' => 'text',
            'display_name' => 'موقع سناب شات'
        ]);

        Setting::firstOrCreate([
            'key' => 'aboutapp',
        ], [
            'value' => 'عن التطبيق',
            'type' => 'text',
            'display_name' => 'عن التطبيق'
        ]);

    }
}
