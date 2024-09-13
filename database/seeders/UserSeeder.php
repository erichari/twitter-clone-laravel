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
                'user_image' => 'images/img/icon-default-user.svg',
                'header_image' => 'images/img/default-header.png',
            ],
            [
                'nickname' => 'じろう',
                'name' => 'jiro',
                'email' => 'jiro@a',
                'password' => Hash::make('password'),
                'profile' => '趣味はクロスバイクです。',
                'user_image' => 'images/img/icon-default-user.svg',
                'header_image' => 'images/img/default-header.png',
            ],
            [
                'nickname' => 'さぶろう',
                'name' => 'saburo',
                'email' => 'saburo@a',
                'password' => Hash::make('password'),
                'profile' => '',
                'user_image' => 'images/img/icon-default-user.svg',
                'header_image' => 'images/img/default-header.png',
            ],
            [
                'nickname' => 'かなちゃん',
                'name' => 'kanachan',
                'email' => 'kanachan@a',
                'password' => Hash::make('password'),
                'profile' => '',
                'user_image' => 'images/img/icon-default-user.svg',
                'header_image' => 'images/img/default-header.png',
            ],
                ]);
    }
}
