<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('users')->insert([
            [
                'nickname' => '太郎',
                'name' => 'taro',
                'email' => 'taro@a',
                'password' => Hash::make('password'),
                'profile' => 'たろうと申します。よろしくお願いします。',
            ],
            [
                'nickname' => 'じろう',
                'name' => 'jiro',
                'email' => 'jiro@a',
                'password' => Hash::make('password'),
                'profile' => '趣味はクロスバイクです。',
            ],
            [
                'nickname' => 'さぶろう',
                'name' => 'saburo',
                'email' => 'saburo@a',
                'password' => Hash::make('password'),
                'profile' => '',
            ],
            [
                'nickname' => 'かなちゃん',
                'name' => 'kanachan',
                'email' => 'kanachan@a',
                'password' => Hash::make('password'),
                'profile' => '',
            ],
                ]);
    }
}
