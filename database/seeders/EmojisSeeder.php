<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmojisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('emojis')->insert([
            ['unicode' => 'U+1F642'],
            ['unicode' => 'U+1F610'],
            ['unicode' => 'U+1F612'],
            ['unicode' => 'U+1FAE3'],
            ['unicode' => 'U+1F62C']
        ]);
    }
}
