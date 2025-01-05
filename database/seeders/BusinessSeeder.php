<?php

namespace Database\Seeders;

use App\Models\Business;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arr=[
            [
                'key' => 'logo',
                'type' => 'file',
                'value'=>'',
                'device_type'=>'app'
            ],
            [
                'key' => 'splash_screen',
                'type' => 'file',
                'value'=>'',
                'device_type'=>'app'
            ],
            [
                'key' => 'primary_color',
                'type' => 'string',
                'value'=>'',
                'device_type'=>'app'
            ],
            [
                'key' => 'secondary_color',
                'type' => 'string',
                'value'=>'',
                'device_type'=>'app'
            ],
            [
                'key' => 'text_color',
                'type' => 'string',
                'value'=>'',
                'device_type'=>'app'
            ],
            [
                'key' => 'google_map_api',
                'type' => 'string',
                'value'=>'',
                'device_type'=>'app'
            ],
            [
                'key' => 'logo',
                'type' => 'file',
                'value'=>'',
                'device_type'=>'web'
            ],
            ];
            foreach($arr as $a)
            {
                Business::create($a);
            }
    }
}
