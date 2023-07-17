<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Account;
use App\Models\Artist;
use App\Models\Format;
use App\Models\Genre;
use App\Models\Label;
use App\Models\Language;
use App\Models\ParentalAdvisory;
use App\Models\Setting;
use App\Models\Subgenre;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        User::create([
            'first_name' => 'Admin',
            'last_name' => '',
            'username' => 'admin',
            'email' => 'mail.dreamrecords@gmail.com',
            'password' => Hash::make('dreamrecords.in'),
            'isAdmin' => true
        ]);

        Setting::create([
            'whatsapp' => '01677756337',
            'min_withdraw' => 300
        ]);

        // Account::create([
        //     'user_id' => 2,
        //     'balance' => 0,
        // ]);

        // Language::create(['name' => 'English']);
        // Language::create(['name' => 'Bangla']);
        // Language::create(['name' => 'Hindi']);

        // Genre::create(['name' => 'Indian']);
        // Genre::create(['name' => 'Tamil']);

        // Subgenre::create(['name' => 'Indian Bangla', 'genre_id' => 1]);
        // Subgenre::create(['name' => 'South Indian', 'genre_id' => 1]);
        // Subgenre::create(['name' => 'Telegu', 'genre_id' => 2]);
        // Subgenre::create(['name' => 'Malayam', 'genre_id' => 2]);

        // Format::create(['name' => 'Single']);
        // Format::create(['name' => 'Album']);

        // ParentalAdvisory::create(['name' => 'Yes']);
        // ParentalAdvisory::create(['name' => 'No']);

        // Artist::create(['title' => 'Anupam Roy', 'user_id' => 2]);
        // Artist::create(['title' => 'Subhamay Karjee', 'user_id' => 2]);

        // Label::create(['title' => 'Test Label', 'user_id' => 2]);
    }
}
